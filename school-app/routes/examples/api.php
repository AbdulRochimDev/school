<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', fn() => ['status' => 'ok']);

// Simple auth for tests
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
// Admin finance for tests
Route::post('/admin/payments/{id}/verify', [\App\Http\Controllers\Admin\PaymentController::class,'verify']);
