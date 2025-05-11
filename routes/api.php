<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\AIInvoiceController;
use App\Http\Controllers\Api\Client\AuthController;
use App\Http\Controllers\Api\Client\InvoiceController as ClientInvoiceController;

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

// Admin API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::post('ai/invoice', [AIInvoiceController::class, 'create']);
});

// Client API routes
Route::prefix('client')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('dashboard', [ClientInvoiceController::class, 'dashboard']);
        Route::get('invoices', [ClientInvoiceController::class, 'index']);
        Route::get('invoices/{id}', [ClientInvoiceController::class, 'show']);
        Route::get('invoices/{id}/payment', [ClientInvoiceController::class, 'payment']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});