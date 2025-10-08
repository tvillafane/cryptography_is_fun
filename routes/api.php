<?php

use App\Http\Controllers\KeysController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [KeysController::class, 'registerPublicKey']);
Route::post('/nonce', [KeysController::class, 'generateNonceForKey']);
Route::post('/verify', [KeysController::class, 'verifyMessage']);
