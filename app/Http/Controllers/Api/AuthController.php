<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Buscar al usuario manualmente por email y password plano
        $user = User::where('email', $request->email)
            ->where('pass', $request->password)
            ->first();

        // 3. Si no existe, error
        if (!$user) {
            return response()->json([
                'error' => 'Credenciales inválidas'
            ], 401);
        }

        // 4. Generar el token manualmente para ese usuario
        // Nota: Esto asume que usas JWT-Auth
        $token = auth()->login($user);

        return response()->json([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email
            ],
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Sesión cerrada'
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh()
        ]);
    }
}
