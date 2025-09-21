<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('settings.profile', compact('user'));
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ], [
            'email.unique' => 'Este email já está a ser utilizado por outro utilizador.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return back()->with('success', 'Perfil atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Prevent deleting the admin user (ID = 1)
        if ($user->id === 1) {
            return back()->with('error', 'O utilizador administrador principal não pode ser eliminado.');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
        ], [
            'current_password.current_password' => 'A password atual está incorreta.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            // Check if user has related data
            $hasInvoices = $user->invoices()->exists();
            $hasKilometers = $user->kilometers()->exists();
            $hasSalaries = $user->salariesCreated()->exists() || $user->salariesAsEmployee()->exists();

            if ($hasInvoices || $hasKilometers || $hasSalaries) {
                return back()->with('error',
                    'Não é possível eliminar a sua conta pois tem dados associados (faturas, quilometragem ou salários). ' .
                    'Contacte um administrador para transferir os seus dados antes de eliminar a conta.'
                );
            }

            // Log out and delete account
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $user->delete();

            return redirect()->route('login')->with('success', 'Conta eliminada com sucesso.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao eliminar conta: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile data (AJAX)
     */
    public function getData()
    {
        $user = Auth::user();

        try {
            $stats = [
                'total_invoices' => $user->invoices()->count(),
                'total_kilometers' => $user->kilometers()->count(),
                'total_salaries_created' => $user->salariesCreated()->count(),
                'total_salaries_as_employee' => $user->salariesAsEmployee()->count(),
                'account_created' => $user->created_at->format('d/m/Y'),
                'last_updated' => $user->updated_at->format('d/m/Y H:i'),
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->value,
                        'role_label' => $user->role->label(),
                        'email_verified_at' => $user->email_verified_at?->format('d/m/Y H:i'),
                    ],
                    'stats' => $stats
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados do perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update email preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'notifications_enabled' => 'boolean',
            'email_reports' => 'boolean',
            'weekly_summary' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // For this implementation, we'll store preferences in a JSON field
            // In a real app, you might have a separate user_preferences table

            $preferences = [
                'notifications_enabled' => $request->boolean('notifications_enabled', true),
                'email_reports' => $request->boolean('email_reports', false),
                'weekly_summary' => $request->boolean('weekly_summary', true),
            ];

            // If you had a preferences field in users table:
            // $user->update(['preferences' => $preferences]);

            return response()->json([
                'success' => true,
                'message' => 'Preferências atualizadas com sucesso!',
                'data' => $preferences
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar preferências: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportData()
    {
        $user = Auth::user();

        try {
            $data = [
                'user_information' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->label(),
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                    'email_verified_at' => $user->email_verified_at?->toISOString(),
                ],
                'invoices_created' => $user->invoices()->with(['company', 'category'])->get()->map(function ($invoice) {
                    return [
                        'atcud' => $invoice->atcud,
                        'type' => $invoice->type_label,
                        'company' => $invoice->company->name,
                        'category' => $invoice->category?->name,
                        'date' => $invoice->date->toDateString(),
                        'total' => $invoice->total,
                        'created_at' => $invoice->created_at->toISOString(),
                    ];
                }),
                'kilometers_registered' => $user->kilometers()->with('company')->get()->map(function ($km) {
                    return [
                        'company' => $km->company->name,
                        'date' => $km->date->toDateString(),
                        'origin' => $km->origin,
                        'destination' => $km->destination,
                        'kilometers' => $km->kilometers,
                        'license_plate' => $km->licenseplate,
                        'reason' => $km->reason,
                        'created_at' => $km->created_at->toISOString(),
                    ];
                }),
                'salaries_created' => $user->salariesCreated()->with(['company', 'employee'])->get()->map(function ($salary) {
                    return [
                        'company' => $salary->company->name,
                        'employee' => $salary->employee->name,
                        'date' => $salary->date->toDateString(),
                        'gross_salary' => $salary->gross_salary_month,
                        'created_at' => $salary->created_at->toISOString(),
                    ];
                }),
                'salaries_as_employee' => $user->salariesAsEmployee()->with('company')->get()->map(function ($salary) {
                    return [
                        'company' => $salary->company->name,
                        'date' => $salary->date->toDateString(),
                        'gross_salary' => $salary->gross_salary_month,
                        'food_allowance' => $salary->food_allowance_month,
                        'created_at' => $salary->created_at->toISOString(),
                    ];
                }),
            ];

            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $fileName = 'dados_pessoais_' . $user->id . '_' . now()->format('Y-m-d') . '.json';

            return response($json)
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao exportar dados: ' . $e->getMessage());
        }
    }
}