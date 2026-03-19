<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;

Route::prefix('v1')->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::post('{account_number}/withdraw', [AccountController::class, 'withdraw']);
        Route::post('{account_number}/deposit', [AccountController::class, 'deposit']);
        Route::get('{account_number}/balance', [AccountController::class, 'balance']);
    });
});