<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ExchangeRateController;

Route::middleware('auth')->group(function () {
    Route::resource('asset/bank-accounts', BankAccountController::class);
    Route::resource('asset/exchange-rates', ExchangeRateController::class);
});