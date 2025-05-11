<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AIInvoiceController;
use App\Http\Controllers\Api\Client\AuthController;
use App\Http\Controllers\Api\Client\InvoiceController as ClientInvoiceController;
use App\Http\Controllers\Api\Client\ReceiptController as ClientReceiptController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Admin API routes - protected by auth:sanctum and role middleware
Route::middleware(['auth:sanctum', 'role:accountant,auditor'])->group(function () {
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::post('ai/invoice', [AIInvoiceController::class, 'create']);
});

// Client API routes
Route::prefix('client')->group(function () {
    // Public routes for authentication
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    // Protected routes - only return the authenticated customer's own data
    Route::middleware('auth:sanctum')->group(function () {
        // Dashboard data
        Route::get('dashboard', [ClientInvoiceController::class, 'dashboard']);

        // Invoices
        Route::get('invoices', [ClientInvoiceController::class, 'index']);
        Route::get('invoices/{id}', [ClientInvoiceController::class, 'show']);
        Route::get('invoices/{id}/payment', [ClientInvoiceController::class, 'payment']);

        // Receipts
        Route::get('receipts', [ClientReceiptController::class, 'index']);
        Route::get('receipts/{id}', [ClientReceiptController::class, 'show']);

        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
    });
});