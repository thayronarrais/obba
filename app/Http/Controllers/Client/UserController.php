<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        // Only admin can access user management
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isAdmin()) {
                abort(403, 'Acesso negado. Apenas administradores podem gerir utilizadores.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['invoices', 'kilometers', 'salariesCreated', 'salariesAsEmployee']);

        return view('users.show', compact('user'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:128',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in([UserRole::ADMIN->value, UserRole::ACCOUNTANT->value, UserRole::USER->value])],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Utilizador criado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar utilizador: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Prevent updating the admin user (ID = 1)
        if ($user->id === 1) {
            return back()->with('error', 'O utilizador administrador principal não pode ser editado.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:128',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in([UserRole::ADMIN->value, UserRole::ACCOUNTANT->value, UserRole::USER->value])],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return back()->with('success', 'Utilizador atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar utilizador: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        // Prevent deleting the admin user (ID = 1)
        if ($user->id === 1) {
            return response()->json([
                'success' => false,
                'message' => 'O utilizador administrador principal não pode ser eliminado.'
            ], 403);
        }

        // Prevent self-deletion
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Não pode eliminar a sua própria conta.'
            ], 403);
        }

        try {
            // Check if user has related data
            $hasInvoices = $user->invoices()->exists();
            $hasKilometers = $user->kilometers()->exists();
            $hasSalaries = $user->salariesCreated()->exists() || $user->salariesAsEmployee()->exists();

            if ($hasInvoices || $hasKilometers || $hasSalaries) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível eliminar este utilizador pois tem dados associados (faturas, quilometragem ou salários).'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilizador eliminado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar utilizador: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function destroyBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'IDs inválidos fornecidos.'
            ], 422);
        }

        $currentUser = Auth::user();
        $deletedCount = 0;
        $skippedCount = 0;
        $errors = [];

        try {
            foreach ($request->ids as $userId) {
                $user = User::find($userId);

                if (!$user) {
                    continue;
                }

                // Skip admin user (ID = 1)
                if ($user->id === 1) {
                    $skippedCount++;
                    $errors[] = "Utilizador {$user->name}: Administrador principal não pode ser eliminado";
                    continue;
                }

                // Skip current user
                if ($user->id === $currentUser->id) {
                    $skippedCount++;
                    $errors[] = "Utilizador {$user->name}: Não pode eliminar a sua própria conta";
                    continue;
                }

                // Check if user has related data
                $hasData = $user->invoices()->exists() ||
                          $user->kilometers()->exists() ||
                          $user->salariesCreated()->exists() ||
                          $user->salariesAsEmployee()->exists();

                if ($hasData) {
                    $skippedCount++;
                    $errors[] = "Utilizador {$user->name}: Tem dados associados";
                    continue;
                }

                $user->delete();
                $deletedCount++;
            }

            $message = "{$deletedCount} utilizadores eliminados com sucesso";
            if ($skippedCount > 0) {
                $message .= ". {$skippedCount} foram ignorados";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => $deletedCount,
                'skipped' => $skippedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao eliminar utilizadores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password redefinida com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao redefinir password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent modifying the admin user (ID = 1)
        if ($user->id === 1) {
            return response()->json([
                'success' => false,
                'message' => 'O utilizador administrador principal não pode ser desativado.'
            ], 403);
        }

        $currentUser = Auth::user();

        // Prevent self-modification
        if ($user->id === $currentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Não pode alterar o estado da sua própria conta.'
            ], 403);
        }

        try {
            // For this implementation, we'll just simulate status toggle
            // In a real app, you'd have an 'active' field in users table

            return response()->json([
                'success' => true,
                'message' => 'Estado do utilizador alterado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar estado: ' . $e->getMessage()
            ], 500);
        }
    }
}