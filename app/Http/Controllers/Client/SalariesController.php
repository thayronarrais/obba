<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\Company;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SalariesController extends Controller
{
    /**
     * Display a listing of salaries
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Salary::with(['company', 'employee', 'creator']);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        // Apply filters
        if ($request->filled('company_id')) {
            $query->forCompany($request->company_id);
        }

        if ($request->filled('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        if ($request->filled('year')) {
            $query->inYear($request->year);
        }

        if ($request->filled('month')) {
            $query->inMonth($request->month);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->betweenDates($request->date_from, $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $salaries = $query->orderBy('date', 'desc')->paginate(20);

        // Statistics
        $totalSalaries = $query->sum('gross_salary_month');
        $totalBenefits = $query->get()->sum(fn($s) => $s->total_benefits);
        $totalCost = $query->get()->sum(fn($s) => $s->total_cost);
        $totalRecords = $query->count();

        // Get filter options
        $companies = Company::orderBy('name')->get();
        $employees = User::where('role', UserRole::USER->value)->orderBy('name')->get();
        $years = Salary::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('salaries.index', compact(
            'salaries',
            'companies',
            'employees',
            'years',
            'totalSalaries',
            'totalBenefits',
            'totalCost',
            'totalRecords'
        ));
    }

    /**
     * Show the form for creating a new salary
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $employees = User::where('role', UserRole::USER->value)->orderBy('name')->get();

        return view('salaries.create', compact('companies', 'employees'));
    }

    /**
     * Get last salary for employee (AJAX)
     */
    public function salary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'ID do funcionário inválido']);
        }

        $lastSalary = Salary::getLastSalaryForEmployee($request->employee_id);

        if ($lastSalary) {
            return response()->json([
                'success' => true,
                'data' => [
                    'gross_salary_month' => $lastSalary->gross_salary_month,
                    'food_allowance_month' => $lastSalary->food_allowance_month,
                    'additional_subsidies' => $lastSalary->additional_subsidies,
                    'social_security' => $lastSalary->social_security,
                    'mandatory_ensurance' => $lastSalary->mandatory_ensurance,
                ]
            ]);
        } else {
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Nenhum salário anterior encontrado para este funcionário'
            ]);
        }
    }

    /**
     * Store a newly created salary
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employeeId' => 'required|exists:users,id',
            'salary' => 'nullable|numeric|min:0',
            'foodAllowance' => 'nullable|numeric|min:0',
            'additionalSubsidies' => 'nullable|numeric|min:0',
            'socialSecurity' => 'nullable|numeric|min:0',
            'ensurance' => 'nullable|numeric|min:0',
            'companyId' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate that employee has USER role
        $employee = User::findOrFail($request->employeeId);
        if ($employee->role !== UserRole::USER) {
            return back()->withInput()
                ->with('error', 'Apenas utilizadores com papel "USER" podem ser selecionados como funcionários.');
        }

        try {
            Salary::create([
                'date' => $request->date,
                'employee_id' => $request->employeeId,
                'gross_salary_month' => $request->salary ?? 0,
                'food_allowance_month' => $request->foodAllowance ?? 0,
                'additional_subsidies' => $request->additionalSubsidies ?? 0,
                'social_security' => $request->socialSecurity ?? 0,
                'mandatory_ensurance' => $request->ensurance ?? 0,
                'company_id' => $request->companyId,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('salaries.index')
                ->with('success', 'Salário registado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao registar salário: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified salary
     */
    public function destroy(Salary $salary)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $salary->created_by !== $user->id) {
            abort(403, 'Não tem permissão para eliminar este salário.');
        }

        try {
            $salary->delete();

            return response()->json([
                'success' => true,
                'message' => 'Salário eliminado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar salário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete salaries
     */
    public function destroyBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:salaries,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'IDs inválidos fornecidos.'
            ], 422);
        }

        $user = Auth::user();
        $query = Salary::whereIn('id', $request->ids);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        try {
            $deletedCount = $query->count();
            $query->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} salários eliminados com sucesso!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar salários: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get salary statistics for dashboard
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $query = Salary::query();

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        if ($request->filled('company_id')) {
            $query->forCompany($request->company_id);
        }

        if ($request->filled('year')) {
            $query->inYear($request->year);
        }

        if ($request->filled('month')) {
            $query->inMonth($request->month);
        }

        $salaries = $query->get();
        $totalEmployees = $salaries->unique('employee_id')->count();
        $totalGrossSalary = $salaries->sum('gross_salary_month');
        $totalBenefits = $salaries->sum(fn($s) => $s->total_benefits);
        $totalSocialSecurity = $salaries->sum('social_security');
        $totalInsurance = $salaries->sum('mandatory_ensurance');
        $totalCost = $salaries->sum(fn($s) => $s->total_cost);
        $averageSalary = $totalEmployees > 0 ? $totalGrossSalary / $totalEmployees : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_employees' => $totalEmployees,
                'total_gross_salary' => $totalGrossSalary,
                'total_benefits' => $totalBenefits,
                'total_social_security' => $totalSocialSecurity,
                'total_insurance' => $totalInsurance,
                'total_cost' => $totalCost,
                'average_salary' => round($averageSalary, 2),
                'formatted_total_cost' => number_format($totalCost, 2, ',', '.') . ' €',
                'formatted_average_salary' => number_format($averageSalary, 2, ',', '.') . ' €',
            ]
        ]);
    }

    /**
     * Export salary report for a period
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'company_id' => 'nullable|exists:companies,id',
            'employee_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();
        $query = Salary::with(['company', 'employee', 'creator'])
            ->betweenDates($request->start_date, $request->end_date);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        if ($request->filled('company_id')) {
            $query->forCompany($request->company_id);
        }

        if ($request->filled('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        $salaries = $query->orderBy('date', 'desc')->get();

        if ($salaries->isEmpty()) {
            return back()->with('error', 'Nenhum salário encontrado para o período selecionado.');
        }

        // Generate CSV
        $csv = $this->generateSalaryCsv($salaries);

        $fileName = 'salarios_' . $request->start_date . '_' . $request->end_date . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }

    /**
     * Generate CSV content for salaries
     */
    private function generateSalaryCsv($salaries)
    {
        $csv = "Data,Funcionário,Email,Empresa,Salário Bruto,Subsídio Alimentação,Subsídios Adicionais,Segurança Social,Seguro Obrigatório,Custo Total\n";

        foreach ($salaries as $salary) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $salary->formatted_date,
                $salary->employee->name,
                $salary->employee->email,
                $salary->company->name,
                $salary->formatted_gross_salary,
                $salary->formatted_food_allowance,
                $salary->formatted_additional_subsidies,
                $salary->formatted_social_security,
                $salary->formatted_mandatory_ensurance,
                $salary->formatted_total_cost
            );
        }

        return $csv;
    }
}