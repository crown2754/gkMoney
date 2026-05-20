<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExchangeRateController;

Route::get('/api/exchange-rate/{code}', [ExchangeRateController::class, 'apiShow']);