<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SalaryController extends Controller
{
    /**
     * Display a listing of salaries
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Salary::with(['company', 'employee', 'creator'])->orderBy('date', 'desc');

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

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('year')) {
            $query->inYear($request->year);
        }

        if ($request->filled('month')) {
            $query->inMonth($request->month);
        }

        $salaries = $query->paginate(10)->withQueryString();

        // Get filter options
        $companies = Company::orderBy('name')->get();
        $employees = User::orderBy('name')->get();
        $years = Salary::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Calculate totals for current filtered results
        $totalQuery = clone $query;
        $totalQuery->getQuery()->limit = null;
        $totalQuery->getQuery()->offset = null;

        $totals = [
            'total_records' => $totalQuery->count(),
            'total_gross_salary' => $totalQuery->sum('gross_salary_month'),
            'total_benefits' => $totalQuery->get()->sum(fn($s) => $s->total_benefits),
            'total_cost' => $totalQuery->get()->sum(fn($s) => $s->total_cost),
        ];

        return view('client.salaries.index', compact(
            'salaries',
            'companies',
            'employees',
            'years',
            'totals'
        ));
    }

    /**
     * Show the form for creating a new salary
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $employees = User::orderBy('name')->get();

        return view('client.salaries.create', compact('companies', 'employees'));
    }

    /**
     * Store a newly created salary
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|exists:users,id',
            'gross_salary_month' => 'required|numeric|min:0|max:999999.99',
            'food_allowance_month' => 'nullable|numeric|min:0|max:999999.99',
            'additional_subsidies' => 'nullable|numeric|min:0|max:999999.99',
            'social_security' => 'nullable|numeric|min:0|max:999999.99',
            'mandatory_ensurance' => 'nullable|numeric|min:0|max:999999.99',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $salary = Salary::create([
                'date' => $request->date,
                'employee_id' => $request->employee_id,
                'gross_salary_month' => $request->gross_salary_month,
                'food_allowance_month' => $request->food_allowance_month ?? 0,
                'additional_subsidies' => $request->additional_subsidies ?? 0,
                'social_security' => $request->social_security ?? 0,
                'mandatory_ensurance' => $request->mandatory_ensurance ?? 0,
                'company_id' => $request->company_id,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('salary.index')
                ->with('success', 'Salário criado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar salário: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified salary
     */
    public function show(Salary $salary)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $salary->created_by !== $user->id) {
            abort(403, 'Não tem permissão para ver este salário.');
        }

        $salary->load(['company', 'employee', 'creator']);

        return view('client.salaries.show', compact('salary'));
    }

    /**
     * Show the form for editing the specified salary
     */
    public function edit(Salary $salary)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $salary->created_by !== $user->id) {
            abort(403, 'Não tem permissão para editar este salário.');
        }

        $companies = Company::orderBy('name')->get();
        $employees = User::orderBy('name')->get();

        return view('client.salaries.edit', compact('salary', 'companies', 'employees'));
    }

    /**
     * Update the specified salary
     */
    public function update(Request $request, Salary $salary)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $salary->created_by !== $user->id) {
            abort(403, 'Não tem permissão para editar este salário.');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'employee_id' => 'required|exists:users,id',
            'gross_salary_month' => 'required|numeric|min:0|max:999999.99',
            'food_allowance_month' => 'nullable|numeric|min:0|max:999999.99',
            'additional_subsidies' => 'nullable|numeric|min:0|max:999999.99',
            'social_security' => 'nullable|numeric|min:0|max:999999.99',
            'mandatory_ensurance' => 'nullable|numeric|min:0|max:999999.99',
            'company_id' => 'required|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $salary->update([
                'date' => $request->date,
                'employee_id' => $request->employee_id,
                'gross_salary_month' => $request->gross_salary_month,
                'food_allowance_month' => $request->food_allowance_month ?? 0,
                'additional_subsidies' => $request->additional_subsidies ?? 0,
                'social_security' => $request->social_security ?? 0,
                'mandatory_ensurance' => $request->mandatory_ensurance ?? 0,
                'company_id' => $request->company_id,
            ]);

            return back()->with('success', 'Salário atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar salário: ' . $e->getMessage());
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

        if ($request->filled('employee_id')) {
            $query->forEmployee($request->employee_id);
        }

        if ($request->filled('year')) {
            $query->inYear($request->year);
        }

        if ($request->filled('month')) {
            $query->inMonth($request->month);
        }

        $salaries = $query->get();
        $totalRecords = $salaries->count();
        $totalGrossSalary = $salaries->sum('gross_salary_month');
        $totalBenefits = $salaries->sum(fn($s) => $s->total_benefits);
        $totalCost = $salaries->sum(fn($s) => $s->total_cost);
        $averageSalary = $totalRecords > 0 ? $totalGrossSalary / $totalRecords : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_records' => $totalRecords,
                'total_gross_salary' => $totalGrossSalary,
                'total_benefits' => $totalBenefits,
                'total_cost' => $totalCost,
                'average_salary' => round($averageSalary, 2),
                'formatted_total_cost' => number_format($totalCost, 2, ',', '.') . ' €',
                'formatted_total_gross_salary' => number_format($totalGrossSalary, 2, ',', '.') . ' €',
                'formatted_average_salary' => number_format($averageSalary, 2, ',', '.') . ' €',
            ]
        ]);
    }
}