<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/me', [\App\Http\Controllers\UserController::class, 'me']);
    });

    Route::middleware('role:ADMIN')->group(function () {

        Route::get('/users', [\App\Http\Controllers\UserController::class, 'getAllUsers']);

        Route::prefix('registration-codes')->group(function () {
            Route::get('', [\App\Http\Controllers\RegistrationCodeController::class, 'getRegistrationCode']);
            Route::post('', [\App\Http\Controllers\RegistrationCodeController::class, 'store']);
            Route::delete('{code}', [\App\Http\Controllers\RegistrationCodeController::class, 'destroy']);
        });
    });
});

