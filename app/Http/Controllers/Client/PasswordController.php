<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Show the form for changing password
     */
    public function edit()
    {
        return view('settings.password');
    }

    /**
     * Update the user's password
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'A password atual está incorreta.',
            'password.min' => 'A nova password deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da password não coincide.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return back()->with('success', 'Password alterada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao alterar password: ' . $e->getMessage());
        }
    }

    /**
     * Update password via AJAX
     */
    public function updateAjax(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'A password atual está incorreta.',
            'password.min' => 'A nova password deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da password não coincide.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password alterada com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate current password (AJAX)
     */
    public function validateCurrentPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password atual é obrigatória'
            ]);
        }

        $user = Auth::user();
        $isValid = Hash::check($request->current_password, $user->password);

        return response()->json([
            'success' => true,
            'valid' => $isValid,
            'message' => $isValid ? 'Password atual correcta' : 'Password atual incorreta'
        ]);
    }

    /**
     * Check password strength (AJAX)
     */
    public function checkPasswordStrength(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password é obrigatória'
            ]);
        }

        $password = $request->password;
        $score = 0;
        $feedback = [];

        // Length check
        if (strlen($password) >= 8) {
            $score += 1;
        } else {
            $feedback[] = 'Deve ter pelo menos 8 caracteres';
        }

        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Deve conter pelo menos uma letra maiúscula';
        }

        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Deve conter pelo menos uma letra minúscula';
        }

        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Deve conter pelo menos um número';
        }

        // Special character check
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score += 1;
        } else {
            $feedback[] = 'Deve conter pelo menos um caracter especial';
        }

        // Length bonus
        if (strlen($password) >= 12) {
            $score += 1;
        }

        // Calculate percentage and strength level
        $percentage = min(100, ($score / 6) * 100);

        if ($percentage < 40) {
            $strength = 'weak';
            $label = 'Fraca';
            $color = 'danger';
        } elseif ($percentage < 70) {
            $strength = 'medium';
            $label = 'Média';
            $color = 'warning';
        } else {
            $strength = 'strong';
            $label = 'Forte';
            $color = 'success';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'score' => $score,
                'percentage' => $percentage,
                'strength' => $strength,
                'label' => $label,
                'color' => $color,
                'feedback' => $feedback,
                'valid' => $score >= 2 && strlen($password) >= 8, // Minimum requirements
            ]
        ]);
    }

    /**
     * Generate secure password suggestion (AJAX)
     */
    public function generatePassword(Request $request)
    {
        $length = $request->input('length', 12);
        $length = max(8, min(32, $length)); // Ensure reasonable length

        $includeUppercase = $request->boolean('uppercase', true);
        $includeLowercase = $request->boolean('lowercase', true);
        $includeNumbers = $request->boolean('numbers', true);
        $includeSymbols = $request->boolean('symbols', true);

        $characters = '';

        if ($includeLowercase) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($includeUppercase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($includeNumbers) {
            $characters .= '0123456789';
        }

        if ($includeSymbols) {
            $characters .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }

        if (empty($characters)) {
            return response()->json([
                'success' => false,
                'message' => 'Deve selecionar pelo menos um tipo de caracter'
            ]);
        }

        try {
            $password = '';
            $charactersLength = strlen($characters);

            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[random_int(0, $charactersLength - 1)];
            }

            // Ensure password meets minimum requirements
            $hasLower = preg_match('/[a-z]/', $password);
            $hasUpper = preg_match('/[A-Z]/', $password);
            $hasNumber = preg_match('/[0-9]/', $password);

            $meetsRequirements = $hasLower && $hasUpper && $hasNumber && strlen($password) >= 8;

            return response()->json([
                'success' => true,
                'data' => [
                    'password' => $password,
                    'length' => strlen($password),
                    'meets_requirements' => $meetsRequirements,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get password security tips
     */
    public function getSecurityTips()
    {
        $tips = [
            'Use pelo menos 8 caracteres (recomendado: 12 ou mais)',
            'Combine letras maiúsculas e minúsculas',
            'Inclua números e caracteres especiais',
            'Evite informações pessoais (nome, data de nascimento, etc.)',
            'Não reutilize passwords de outras contas',
            'Use um gestor de passwords para passwords únicas',
            'Altere a password regularmente',
            'Nunca partilhe a sua password com ninguém',
            'Use autenticação de dois fatores quando disponível',
            'Evite passwords comuns como "123456" ou "password"',
        ];

        return response()->json([
            'success' => true,
            'data' => $tips
        ]);
    }
}