<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/nonce', [AuthController::class, 'generateNonceForKey']);
Route::post('/verify', [AuthController::class, 'verifyMessage']);
Route::post('/register', [AuthController::class, 'registerPublicKey']);