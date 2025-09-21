<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Enums\CompanyLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Store a newly created company
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nif' => 'required|string|max:12|unique:companies',
            'address' => 'nullable|string',
            'location' => ['required', Rule::in([CompanyLocation::MAINLAND->value, CompanyLocation::AZORES->value, CompanyLocation::MADEIRA->value])],
            'iva_monthly_period' => 'required|boolean',
        ], [
            'nif.unique' => 'Este NIF já está registado no sistema.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $company = Company::create([
                'name' => $request->name,
                'nif' => $request->nif,
                'address' => $request->address,
                'location' => $request->location,
                'iva_monthly_period' => $request->iva_monthly_period,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Empresa criada com sucesso!',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'nif' => $company->nif,
                    'formatted_nif' => $company->formatted_nif,
                    'address' => $company->address,
                    'location' => $company->location->value,
                    'location_label' => $company->location_label,
                    'iva_monthly_period' => $company->iva_monthly_period,
                    'iva_period_label' => $company->iva_period_label,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified company
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'nif' => ['required', 'string', 'max:12', Rule::unique('companies')->ignore($request->company_id)],
            'address' => 'nullable|string',
            'location' => ['required', Rule::in([CompanyLocation::MAINLAND->value, CompanyLocation::AZORES->value, CompanyLocation::MADEIRA->value])],
            'iva_monthly_period' => 'required|boolean',
        ], [
            'nif.unique' => 'Este NIF já está registado no sistema.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $company = Company::findOrFail($request->company_id);

            $company->update([
                'name' => $request->name,
                'nif' => $request->nif,
                'address' => $request->address,
                'location' => $request->location,
                'iva_monthly_period' => $request->iva_monthly_period,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Empresa atualizada com sucesso!',
                'data' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'nif' => $company->nif,
                    'formatted_nif' => $company->formatted_nif,
                    'address' => $company->address,
                    'location' => $company->location->value,
                    'location_label' => $company->location_label,
                    'iva_monthly_period' => $company->iva_monthly_period,
                    'iva_period_label' => $company->iva_period_label,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all companies for select options
     */
    public function getOptions()
    {
        try {
            $companies = Company::orderBy('name')->get();

            return response()->json([
                'success' => true,
                'data' => $companies->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'nif' => $company->nif,
                        'formatted_nif' => $company->formatted_nif,
                        'location_label' => $company->location_label,
                        'full_name' => "{$company->name} ({$company->formatted_nif})",
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar empresas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company details
     */
    public function show(Company $company)
    {
        try {
            $company->load(['invoices', 'kilometers', 'salaries']);

            // Company statistics
            $stats = [
                'total_invoices' => $company->invoices()->count(),
                'total_expenses' => $company->invoices()->where('type', 1)->sum('total'),
                'total_sales' => $company->invoices()->where('type', 2)->sum('total'),
                'total_kilometers' => $company->kilometers()->sum('kilometers'),
                'total_salaries' => $company->salaries()->count(),
                'total_salary_cost' => $company->salaries()->get()->sum(fn($s) => $s->total_cost),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => [
                        'id' => $company->id,
                        'name' => $company->name,
                        'nif' => $company->nif,
                        'formatted_nif' => $company->formatted_nif,
                        'address' => $company->address,
                        'location' => $company->location->value,
                        'location_label' => $company->location_label,
                        'iva_monthly_period' => $company->iva_monthly_period,
                        'iva_period_label' => $company->iva_period_label,
                    ],
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados da empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a company
     */
    public function destroy(Company $company)
    {
        try {
            // Check if company has related data
            $hasInvoices = $company->invoices()->exists();
            $hasKilometers = $company->kilometers()->exists();
            $hasSalaries = $company->salaries()->exists();

            if ($hasInvoices || $hasKilometers || $hasSalaries) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível eliminar esta empresa pois tem dados associados (faturas, quilometragem ou salários).'
                ], 400);
            }

            $company->delete();

            return response()->json([
                'success' => true,
                'message' => 'Empresa eliminada com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar empresa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate NIF format and uniqueness
     */
    public function validateNif(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nif' => 'required|string|max:12',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'NIF é obrigatório'
            ]);
        }

        $query = Company::where('nif', $request->nif);

        // If updating, ignore current company
        if ($request->filled('company_id')) {
            $query->where('id', '!=', $request->company_id);
        }

        $exists = $query->exists();

        return response()->json([
            'success' => true,
            'valid' => !$exists,
            'message' => $exists ? 'Este NIF já está registado no sistema' : 'NIF disponível'
        ]);
    }
}