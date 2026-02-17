<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     */
    protected $levels = [
        //
    ];

    /**
     * Exceptions that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * Inputs that are never flashed.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register exception handling.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {

            // Log errores críticos
            Log::error('Error del sistema', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => auth()->id() ?? null,
                'ip'      => request()->ip()
            ]);

        });
    }

    /**
     * Render exceptions to JSON (API)
     */
    public function render($request, Throwable $e)
    {
        // =============================
        // No autenticado
        // =============================
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => 'No autenticado'
            ], 401);
        }

        // =============================
        // No autorizado
        // =============================
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'error' => 'No autorizado'
            ], 403);
        }

        // =============================
        // Validación
        // =============================
        if ($e instanceof ValidationException) {
            return response()->json([
                'error'   => 'Datos inválidos',
                'details' => $e->errors()
            ], 422);
        }

        // =============================
        // Modelo no encontrado
        // =============================
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'error' => 'Recurso no encontrado'
            ], 404);
        }

        // =============================
        // Ruta no encontrada
        // =============================
        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'error' => 'Ruta no encontrada'
            ], 404);
        }

        // =============================
        // Errores personalizados
        // =============================
        if ($e instanceof \Exception) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }

        // =============================
        // Error interno (fallback)
        // =============================
        return response()->json([
            'error' => 'Error interno del servidor'
        ], 500);
    }
}
