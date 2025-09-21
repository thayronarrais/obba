<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\InvoiceCategory;
use App\Enums\InvoiceType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use ZipArchive;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['company', 'creator', 'categoryData'])->orderBy('date', 'desc');

        // Apply filters
        if ($request->filled('tipo')) {
            $query->where('type', $request->tipo);
        }

        if ($request->filled('categoria')) {
            $query->where('category_id', $request->categoria);
        }

        if ($request->filled('nif')) {
            $query->where('nif', 'like', '%' . $request->nif . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $invoices = $query->paginate(10)->withQueryString();

        // Get filter options
        $companies = Company::orderBy('name')->get();
        $categories = InvoiceCategory::active()->ordered()->get();
        $years = Invoice::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('client.invoices.index', compact(
            'companies',
            'categories',
            'invoices',
            'years'
        ));
    }

   
    public function create(){
        return view('client.invoice.create');
    }
    /**
     * Check if invoice exists (AJAX)
     */
    public function exists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'atcud' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'ATCUD é obrigatório']);
        }

        $exists = Invoice::where('atcud', $request->atcud)->exists();

        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'Fatura já existe no sistema' : 'Fatura não encontrada'
        ]);
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoiceAtcud' => 'required|string|unique:invoices,atcud',
            'invoiceType' => ['required', Rule::in([InvoiceType::EXPENSE->value, InvoiceType::SALE->value])],
            'invoiceCategoryId' => 'required_if:invoiceType,' . InvoiceType::EXPENSE->value . '|exists:invoice_categories,id',
            'invoiceCompanyId' => 'required|exists:companies,id',
            'invoiceImage' => 'required|image|mimes:png|max:10240',
            'invoiceData' => 'required|array',
            'invoiceData.A' => 'required|string', // NIF emissor
            'invoiceData.B' => 'nullable|string', // NIF adquirente
            'invoiceData.F' => 'required|date', // Data
            'invoiceData.H' => 'required|string', // ATCUD
            'invoiceData.N' => 'required|numeric|min:0', // Total IVA
            'invoiceData.O' => 'required|numeric|min:0', // Total
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $company = Company::findOrFail($request->invoiceCompanyId);
            $date = Carbon::parse($request->invoiceData['F']);

            // Validate business rules
            $this->validateBusinessRules($request, $company);

            // Store file
            $fileName = $this->storeInvoiceFile($request->file('invoiceImage'), $date);

            // Create invoice
            $invoice = Invoice::create([
                'type' => $request->invoiceType,
                'category_id' => $request->invoiceCategoryId ?? null,
                'atcud' => $request->invoiceAtcud,
                'nif' => $request->invoiceData['A'],
                'date' => $date,
                'total_iva' => $request->invoiceData['N'],
                'total' => $request->invoiceData['O'],
                'files' => $fileName,
                'metadata' => $request->invoiceData,
                'company_id' => $request->invoiceCompanyId,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('invoice.show', $invoice)
                ->with('success', 'Fatura criada com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withInput()
                ->with('error', 'Erro ao criar fatura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $invoice->created_by !== $user->id) {
            abort(403, 'Não tem permissão para ver esta fatura.');
        }

        $invoice->load(['company', 'category', 'creator']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $invoice->created_by !== $user->id) {
            abort(403, 'Não tem permissão para editar esta fatura.');
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|exists:invoice_categories,id',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $invoice->update([
                'category_id' => $request->category_id,
                'company_id' => $request->company_id,
            ]);

            return back()->with('success', 'Fatura atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar fatura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $invoice->created_by !== $user->id) {
            abort(403, 'Não tem permissão para eliminar esta fatura.');
        }

        try {
            // Delete file
            if ($invoice->fileExists()) {
                unlink($invoice->full_file_path);
            }

            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fatura eliminada com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar fatura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete invoices
     */
    public function destroyBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:invoices,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'IDs inválidos fornecidos.'
            ], 422);
        }

        $user = Auth::user();
        $query = Invoice::whereIn('id', $request->ids);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        try {
            $invoices = $query->get();
            $deletedCount = 0;

            foreach ($invoices as $invoice) {
                // Delete file
                if ($invoice->fileExists()) {
                    unlink($invoice->full_file_path);
                }

                $invoice->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} faturas eliminadas com sucesso!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar faturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download invoice file
     */
    public function download(Invoice $invoice)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $invoice->created_by !== $user->id) {
            abort(403);
        }

        if (!$invoice->fileExists()) {
            abort(404, 'Ficheiro não encontrado.');
        }

        $zip = new ZipArchive();
        $zipFileName = storage_path('temp/invoice_' . $invoice->id . '_' . time() . '.zip');

        // Ensure temp directory exists
        if (!file_exists(dirname($zipFileName))) {
            mkdir(dirname($zipFileName), 0755, true);
        }

        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            // Add invoice file
            $zip->addFile($invoice->full_file_path, 'fatura_' . $invoice->atcud . '.png');

            // Add metadata CSV
            $csvContent = $this->generateInvoiceCsv([$invoice]);
            $zip->addFromString('metadados.csv', $csvContent);

            $zip->close();

            return response()->download($zipFileName)->deleteFileAfterSend(true);
        }

        abort(500, 'Erro ao criar arquivo ZIP.');
    }

    /**
     * Bulk download invoices
     */
    public function downloadBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:invoices,id',
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'IDs inválidos fornecidos.');
        }

        $user = Auth::user();
        $query = Invoice::whereIn('id', $request->ids)->with(['company', 'category']);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'Nenhuma fatura encontrada.');
        }

        $zip = new ZipArchive();
        $zipFileName = storage_path('temp/faturas_' . time() . '.zip');

        // Ensure temp directory exists
        if (!file_exists(dirname($zipFileName))) {
            mkdir(dirname($zipFileName), 0755, true);
        }

        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($invoices as $invoice) {
                if ($invoice->fileExists()) {
                    $zip->addFile(
                        $invoice->full_file_path,
                        $invoice->date->format('Y/m') . '/fatura_' . $invoice->atcud . '.png'
                    );
                }
            }

            // Add consolidated CSV
            $csvContent = $this->generateInvoiceCsv($invoices);
            $zip->addFromString('metadados_consolidados.csv', $csvContent);

            $zip->close();

            return response()->download($zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Erro ao criar arquivo ZIP.');
    }

    /**
     * Upload invoices in bulk (PDF)
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoices' => 'required|array',
            'invoices.*.file' => 'required|file|mimes:pdf|max:3072',
            'invoices.*.data' => 'required|array',
            'invoiceType' => ['required', Rule::in([InvoiceType::EXPENSE->value, InvoiceType::SALE->value])],
            'invoiceCategoryId' => 'required_if:invoiceType,' . InvoiceType::EXPENSE->value . '|exists:invoice_categories,id',
            'companyId' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $successful = 0;
        $failed = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            foreach ($request->invoices as $index => $invoiceData) {
                try {
                    $data = $invoiceData['data'];
                    $date = Carbon::parse($data['F']);

                    // Check if ATCUD already exists
                    if (Invoice::where('atcud', $data['H'])->exists()) {
                        $failed++;
                        $errors[] = "Fatura {$index}: ATCUD {$data['H']} já existe";
                        continue;
                    }

                    // Store file
                    $fileName = $this->storeInvoiceFile($invoiceData['file'], $date);

                    // Create invoice
                    Invoice::create([
                        'type' => $request->invoiceType,
                        'category_id' => $request->invoiceCategoryId ?? null,
                        'atcud' => $data['H'],
                        'nif' => $data['A'],
                        'date' => $date,
                        'total_iva' => $data['N'],
                        'total' => $data['O'],
                        'files' => $fileName,
                        'metadata' => $data,
                        'company_id' => $request->companyId,
                        'created_by' => Auth::id(),
                    ]);

                    $successful++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Fatura {$index}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$successful} faturas criadas com sucesso. {$failed} falharam.",
                'successful' => $successful,
                'failed' => $failed,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Erro no upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(Request $request, Company $company)
    {
        $invoiceType = InvoiceType::from($request->invoiceType);
        $emitterNif = $request->invoiceData['A'];
        $acquirerNif = $request->invoiceData['B'] ?? null;

        if ($invoiceType === InvoiceType::EXPENSE) {
            // For expenses, acquirer NIF must match company NIF
            if ($acquirerNif && $acquirerNif != $company->nif) {
                throw new \Exception('Para despesas, o NIF do adquirente deve corresponder ao NIF da empresa.');
            }
        } elseif ($invoiceType === InvoiceType::SALE) {
            // For sales, emitter NIF must match company NIF
            if ($emitterNif != $company->nif) {
                throw new \Exception('Para vendas, o NIF do emissor deve corresponder ao NIF da empresa.');
            }
        }
    }

    /**
     * Store invoice file in organized folder structure
     */
    private function storeInvoiceFile($file, Carbon $date)
    {
        $year = $date->year;
        $month = $date->format('m');
        $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        $path = "invoices/{$year}/{$month}";

        // Ensure directory exists
        $fullPath = storage_path("app/{$path}");
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
        }

        $file->storeAs($path, $fileName);

        return $fileName;
    }

    /**
     * Generate CSV content for invoices
     */
    private function generateInvoiceCsv($invoices)
    {
        $csv = "ATCUD,Tipo,Empresa,Categoria,NIF,Data,Total IVA,Total,Ficheiro\n";

        foreach ($invoices as $invoice) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $invoice->atcud,
                $invoice->type_label,
                $invoice->company->name,
                $invoice->category ? $invoice->category->name : 'N/A',
                $invoice->formatted_nif,
                $invoice->formatted_date,
                $invoice->formatted_total_iva,
                $invoice->formatted_total,
                $invoice->files
            );
        }

        return $csv;
    }
}
