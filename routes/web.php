<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Public routes
Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');

// Authenticated routes
Route::middleware('auth:web')->group(function () {
    Route::match(['get', 'post'], 'add-user', [AuthController::class, 'addUser'])->middleware('checkAdmin')->name('add-user');
    // Transaction routes
    Route::prefix('transaction')->group(function () {
        // Admin-only routes
        Route::middleware('checkAdmin')->group(function () {
            Route::get('/report', [TransactionController::class, 'generateReport'])->name('transaction.report');
            Route::match(['get', 'post'], '/create', [TransactionController::class, 'create'])->name('transaction.create');
            Route::match(['get', 'post'], '/{transaction}/payment', [TransactionController::class, 'recordPayment'])->name('transaction.payment');
        });

        // General routes accessible to authenticated users
        Route::get('/', [TransactionController::class, 'index'])->name('transaction.index');
    });

    // logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});




