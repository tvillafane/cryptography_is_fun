<?php

use App\Http\Controllers\KeysController;
use Illuminate\Support\Facades\Route;

Route::get('/nonce', [KeysController::class, 'generateNonceForKey']);
Route::post('/verify', [KeysController::class, 'verifyMessage']);
Route::post('/register', [KeysController::class, 'registerPublicKey']);