<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
        Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
        Route::get('/me', [\App\Http\Controllers\AuthController::class, 'me']);
    });
});

Route::prefix('registration-codes')->group(function () {
    Route::get('', [\App\Http\Controllers\RegistrationCodeController::class, 'getRegistrationCode']);
    Route::post('', [\App\Http\Controllers\RegistrationCodeController::class, 'store']);
    Route::delete('{code}', [\App\Http\Controllers\RegistrationCodeController::class, 'destroy']);
});
