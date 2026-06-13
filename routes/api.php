<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('registration-codes')->group(function () {
    Route::get('', [\App\Http\Controllers\RegistrationCodeController::class, 'getRegistrationCode']);
    Route::post('', [\App\Http\Controllers\RegistrationCodeController::class, 'store']);
    Route::delete('{code}', [\App\Http\Controllers\RegistrationCodeController::class, 'destroy']);
});
