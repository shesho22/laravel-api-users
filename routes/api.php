<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;


Route::post('/login',[AuthController::class,'login'])
    ->middleware('throttle:5,1');



Route::middleware('auth:api')->group(function(){

    Route::get('/me',[AuthController::class,'me']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/refresh',[AuthController::class,'refresh']);

    Route::middleware('admin')->group(function(){
        Route::apiResource('users',UserController::class);
    });

});

