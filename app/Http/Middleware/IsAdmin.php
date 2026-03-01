<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificamos si el usuario está autenticado y si su campo admin no es 0
        // Usamos !== 0 por si en la DB es string '1', integer 1 o boolean true
        if (auth()->check() && auth()->user()->admin != 0) {
            return $next($request);
        }

        return response()->json([
            'error' => 'Acceso denegado: Se requieren permisos de administrador'
        ], 403);
    }
}
