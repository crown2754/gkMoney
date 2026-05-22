<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Example login route to test
Route::get('/login', function () {
    return response()->json(['message' => 'Login page']);
});