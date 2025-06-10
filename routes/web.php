<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/home', function () {
    return view('welcome');
});

Route::post('/register', [RegisterController::class, 'store']);