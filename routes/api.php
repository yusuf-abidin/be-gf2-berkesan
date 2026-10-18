<?php

use Illuminate\Support\Facades\Route;

Route::prefix('/auth')->group(function () {
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
});

Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index']);
Route::get('/reset-password/verify', [\App\Http\Controllers\PasswordResetController::class, 'verify']);
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/me', [\App\Http\Controllers\UserController::class, 'me']);
    });

    Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'getAllCategories']);
    Route::get('/accounts', [\App\Http\Controllers\AccountController::class, 'getAllAccounts']);
    Route::get('/accounts/{account}', [\App\Http\Controllers\AccountController::class, 'getAccountById'])->whereNumber('account');
    Route::get('/accounts/{account}/categories', [\App\Http\Controllers\AccountCategoryController::class, 'getCategoriesByAccount'])->whereNumber('account');
    Route::get('/accounts/{account}/periods', [\App\Http\Controllers\PeriodController::class, 'getPeriodsByAccount'])->whereNumber('account');
    Route::get('/periods/{period}/transactions', [\App\Http\Controllers\TransactionController::class, 'getTransactionsByPeriod'])->whereNumber('period');

    Route::get('/public-contributions/types', [\App\Http\Controllers\ContributionController::class, 'getPublicTypes']);
    Route::get('/public-contributions/types/{type}/periods', [\App\Http\Controllers\ContributionController::class, 'getPublicPeriods'])->whereNumber('type');
    Route::get('/public-contributions/periods/{period}/unpaid', [\App\Http\Controllers\ContributionController::class, 'getUnpaidResidents'])->whereNumber('period');

    Route::middleware('role:ADMIN')->group(function () {

        Route::patch('/settings', [\App\Http\Controllers\SettingController::class, 'update']);
        Route::post('/users/{user}/generate-reset-token', [\App\Http\Controllers\PasswordResetController::class, 'generate'])->whereNumber('user');
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'getAllUsers']);
        Route::patch('/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->whereNumber('user');

        Route::prefix('registration-codes')->group(function () {
            Route::get('', [\App\Http\Controllers\RegistrationCodeController::class, 'getRegistrationCode']);
            Route::post('', [\App\Http\Controllers\RegistrationCodeController::class, 'store']);
            Route::delete('{code}', [\App\Http\Controllers\RegistrationCodeController::class, 'destroy']);
        });

        Route::prefix('categories')->group(function () {
            Route::post('', [\App\Http\Controllers\CategoryController::class, 'store']);
            Route::delete('{category}', [\App\Http\Controllers\CategoryController::class, 'destroy']);
            Route::patch('{category}', [\App\Http\Controllers\CategoryController::class, 'update']);
        });

        Route::prefix('transactions')->group(function () {
            Route::post('', [\App\Http\Controllers\TransactionController::class, 'store']);
            Route::patch('{transaction}', [\App\Http\Controllers\TransactionController::class, 'update']);
            Route::delete('{transaction}', [\App\Http\Controllers\TransactionController::class, 'destroy']);
        });

        Route::prefix('accounts')->group(function () {
            Route::post('', [\App\Http\Controllers\AccountController::class, 'store']);
            Route::patch('{account}', [\App\Http\Controllers\AccountController::class, 'update']);
            Route::delete('{account}', [\App\Http\Controllers\AccountController::class, 'destroy']);
        });

        Route::prefix('residents')->group(function () {
            Route::get('', [App\Http\Controllers\ResidentController::class, 'getAllResidents']);
            Route::post('', [App\Http\Controllers\ResidentController::class, 'store']);
            Route::patch('{resident}', [App\Http\Controllers\ResidentController::class, 'update']);
            Route::delete('{resident}', [App\Http\Controllers\ResidentController::class, 'destroy']);
        });

        Route::prefix('coordinators')->group(function () {
            Route::get('', [\App\Http\Controllers\CoordinatorController::class, 'getAllCoordinators']);
            Route::post('', [\App\Http\Controllers\CoordinatorController::class, 'store']);
            Route::patch('{coordinator}', [\App\Http\Controllers\CoordinatorController::class, 'update']);
            Route::delete('{coordinator}', [\App\Http\Controllers\CoordinatorController::class, 'destroy']);
        });

        Route::post('/accounts/{account}/periods', [\App\Http\Controllers\PeriodController::class, 'store'])->whereNumber('account');
        Route::patch('/periods/{period}', [\App\Http\Controllers\PeriodController::class, 'update'])->whereNumber('period');
        Route::delete('/periods/{period}', [\App\Http\Controllers\PeriodController::class, 'destroy'])->whereNumber('period');

        Route::prefix('contribution-types')->group(function () {
            Route::get('', [\App\Http\Controllers\ContributionController::class, 'getTypes']);
            Route::post('', [\App\Http\Controllers\ContributionController::class, 'storeType']);
            Route::patch('{type}', [\App\Http\Controllers\ContributionController::class, 'updateType'])->whereNumber('type');
            Route::delete('{type}', [\App\Http\Controllers\ContributionController::class, 'destroyType'])->whereNumber('type');
            Route::get('{type}/periods', [\App\Http\Controllers\ContributionController::class, 'getPeriods'])->whereNumber('type');
            Route::post('{type}/periods', [\App\Http\Controllers\ContributionController::class, 'storePeriod'])->whereNumber('type');
        });
        Route::get('contribution-periods', [\App\Http\Controllers\ContributionController::class, 'getAllPeriods']);
        Route::delete('contribution-periods/{period}', [\App\Http\Controllers\ContributionController::class, 'destroyPeriod'])->whereNumber('period');
        Route::get('contribution-periods/{period}/payments', [\App\Http\Controllers\ContributionController::class, 'getPayments'])->whereNumber('period');
        Route::patch('contribution-payments/{payment}', [\App\Http\Controllers\ContributionController::class, 'updatePayment'])->whereNumber('payment');
    });
});

