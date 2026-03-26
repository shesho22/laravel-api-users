<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\empresa\EmpresaController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\grupos\UserGroupController;
use App\Http\Controllers\Api\questionarie\UserQuestionnarieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Rutas Públicas (Sin Token)
// El throttle limita a 5 intentos por minuto para evitar ataques de fuerza bruta
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');


// 2. Rutas Protegidas (Requieren Token Válido)
Route::middleware('auth:api')->group(function () {

    // Rutas de Perfil (Cualquier usuario logueado)
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // 3. Rutas de Administración (Requieren Token Y admin != 0)
    Route::middleware('admin')->group(function () {

        /**
         * apiResource genera automáticamente estas 5 rutas:
         * GET    /users          -> index   (Listar)
         * POST   /users          -> store   (Crear)
         * GET    /users/{user}   -> show    (Ver uno)
         * PUT    /users/{user}   -> update  (Editar)
         * DELETE /users/{user}   -> destroy (Eliminar)
        */
        Route::match(['put', 'patch','get'], '/users/{id}/password', [UserController::class, 'updatePassword']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('company', EmpresaController::class);
        Route::apiResource('user-groups',UserGroupController::class);
        Route::get('groups/{groupId}/questionnaries', [UserQuestionnarieController::class, 'questionnariesByGroup']);
        Route::get('groups/{groupId}/questionnaries/{questionariesId}/report', [UserQuestionnarieController::class, 'UsersquestionnariesByGroup']);
    });
});
