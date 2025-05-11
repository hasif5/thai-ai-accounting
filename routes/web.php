<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Admin routes protected by sanctum and role middleware
Route::prefix('admin')->middleware(['auth:sanctum', 'role:accountant,auditor'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::get('/customers', function () {
        return Inertia::render('Admin/Customers');
    })->name('admin.customers');

    Route::get('/invoices', function () {
        return Inertia::render('Admin/Invoices');
    })->name('admin.invoices');

    Route::get('/purchases', function () {
        return Inertia::render('Admin/Purchases');
    })->name('admin.purchases');

    Route::get('/cashflow', function () {
        return Inertia::render('Admin/Cashflow');
    })->name('admin.cashflow');
});

// Client routes protected by customer auth middleware
Route::prefix('client')->group(function () {
    // Public client routes
    Route::get('/login', function () {
        return Inertia::render('Client/Auth/Login');
    })->name('client.login');

    Route::get('/register', function () {
        return Inertia::render('Client/Auth/Register');
    })->name('client.register');

    // Protected client routes
    Route::middleware(['customer.auth'])->group(function () {
        Route::get('/', function () {
            return Inertia::render('Client/Home');
        })->name('client.home');

        Route::get('/invoices', function () {
            return Inertia::render('Client/Invoices');
        })->name('client.invoices');

        Route::get('/invoices/{id}', function ($id) {
            return Inertia::render('Client/InvoiceShow', ['id' => $id]);
        })->name('client.invoices.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
