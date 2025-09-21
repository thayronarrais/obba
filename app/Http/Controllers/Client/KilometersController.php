<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Kilometer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KilometersController extends Controller
{
    /**
     * Display a listing of kilometers
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Kilometer::with(['company', 'creator']);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        // Apply filters
        if ($request->filled('company_id')) {
            $query->forCompany($request->company_id);
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

        if ($request->filled('licenseplate')) {
            $query->byLicensePlate($request->licenseplate);
        }

        if ($request->filled('driver')) {
            $query->byDriver($request->driver);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('licenseplate', 'like', "%{$search}%")
                  ->orWhere('origin', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $kilometers = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();

        // Calculate totals for current filtered results
        $totalQuery = clone $query;
        $totalQuery->getQuery()->limit = null;
        $totalQuery->getQuery()->offset = null;

        $totals = [
            'total_records' => $totalQuery->count(),
            'total_kilometers' => $totalQuery->sum('kilometers'),
            'total_cost' => $totalQuery->sum('kilometers') * 0.36,
        ];

        // Get filter options
        $companies = Company::orderBy('name')->get();
        $years = Kilometer::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('client.kilometers.index', compact(
            'kilometers',
            'companies',
            'years',
            'totals'
        ));
    }

    /**
     * Show the form for creating a new kilometer
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
        return view('client.kilometers.create', compact('companies'));
    }

    /**
     * Store a newly created kilometer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:128',
            'licensePlate' => 'required|string|max:12|regex:/^[A-Z0-9]{2}-[A-Z0-9]{2}-[A-Z0-9]{2}$/',
            'date' => 'required|date',
            'origin' => 'required|string|max:256',
            'destination' => 'required|string|max:256',
            'kilometers' => 'required|integer|min:1|max:999999999',
            'reason' => 'required|string',
            'companyId' => 'required|exists:companies,id',
        ], [
            'licensePlate.regex' => 'A matr�cula deve seguir o formato XX-XX-XX (portugu�s)',
            'kilometers.max' => 'Os quil�metros n�o podem exceder 999.999.999',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Kilometer::create([
                'name' => $request->name,
                'licenseplate' => strtoupper($request->licensePlate),
                'date' => $request->date,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'kilometers' => $request->kilometers,
                'reason' => $request->reason,
                'company_id' => $request->companyId,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('kilometer.index')
                ->with('success', 'Registo de quilometragem criado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar registo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified kilometer
     */
    public function show(Kilometer $kilometer)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $kilometer->created_by !== $user->id) {
            abort(403, 'Não tem permissão para ver este registo.');
        }

        $kilometer->load(['company', 'creator']);

        return view('client.kilometers.show', compact('kilometer'));
    }

    /**
     * Show the form for editing the specified kilometer
     */
    public function edit(Kilometer $kilometer)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $kilometer->created_by !== $user->id) {
            abort(403, 'Não tem permissão para editar este registo.');
        }

        $companies = Company::orderBy('name')->get();

        return view('client.kilometers.edit', compact('kilometer', 'companies'));
    }

    /**
     * Update the specified kilometer
     */
    public function update(Request $request, Kilometer $kilometer)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $kilometer->created_by !== $user->id) {
            abort(403, 'Não tem permissão para editar este registo.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:128',
            'licensePlate' => 'required|string|max:12|regex:/^[A-Z0-9]{2}-[A-Z0-9]{2}-[A-Z0-9]{2}$/',
            'date' => 'required|date',
            'origin' => 'required|string|max:256',
            'destination' => 'required|string|max:256',
            'kilometers' => 'required|integer|min:1|max:999999999',
            'reason' => 'required|string',
            'companyId' => 'required|exists:companies,id',
        ], [
            'licensePlate.regex' => 'A matrícula deve seguir o formato XX-XX-XX (português)',
            'kilometers.max' => 'Os quilómetros não podem exceder 999.999.999',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $kilometer->update([
                'name' => $request->name,
                'licenseplate' => strtoupper($request->licensePlate),
                'date' => $request->date,
                'origin' => $request->origin,
                'destination' => $request->destination,
                'kilometers' => $request->kilometers,
                'reason' => $request->reason,
                'company_id' => $request->companyId,
            ]);

            return back()->with('success', 'Registo atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar registo: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified kilometer
     */
    public function destroy(Kilometer $kilometer)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user->canSeeAllData() && $kilometer->created_by !== $user->id) {
            abort(403, 'N�o tem permiss�o para eliminar este registo.');
        }

        try {
            $kilometer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Registo eliminado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar registo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete kilometers
     */
    public function destroyBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:kilometers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'IDs inv�lidos fornecidos.'
            ], 422);
        }

        $user = Auth::user();
        $query = Kilometer::whereIn('id', $request->ids);

        // Apply role-based filtering
        if (!$user->canSeeAllData()) {
            $query->byCreator($user->id);
        }

        try {
            $deletedCount = $query->count();
            $query->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} registos eliminados com sucesso!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar registos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kilometer statistics for dashboard
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();
        $query = Kilometer::query();

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

        $totalKilometers = $query->sum('kilometers');
        $totalCost = $totalKilometers * 0.36;
        $totalTrips = $query->count();
        $averagePerTrip = $totalTrips > 0 ? $totalKilometers / $totalTrips : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_kilometers' => $totalKilometers,
                'total_cost' => $totalCost,
                'total_trips' => $totalTrips,
                'average_per_trip' => round($averagePerTrip, 2),
                'formatted_total_cost' => number_format($totalCost, 2, ',', '.') . ' �',
                'formatted_total_kilometers' => number_format($totalKilometers, 0, ',', '.') . ' km',
            ]
        ]);
    }
}