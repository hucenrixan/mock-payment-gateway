<?php

use App\Http\Controllers\MerchantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [MerchantController::class, 'index']);
Route::post('/merchants', [MerchantController::class, 'store']);
Route::delete('/merchants/{merchant}', [MerchantController::class, 'destroy']);

// Payment page
Route::get('/pay/{id}', [PaymentController::class, 'show']);
Route::post('/pay/{id}/confirm', [PaymentController::class, 'confirm']);
Route::post('/pay/{id}/decline', [PaymentController::class, 'decline']);

// API routes
Route::post('/api/transaction', [TransactionController::class, 'create']);
Route::get('/api/transaction/{id}', [TransactionController::class, 'show']);
