<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // only admin can add user
    Route::post('/add-user', [AuthController::class, 'addUser'])->middleware('ability:admin');
    // Transaction routes
    Route::prefix('transaction')->group(function () {
        // Admin-only routes
        Route::middleware('ability:admin')->group(function () {
            Route::get('/report', [TransactionController::class, 'generateReport']);
            Route::post('/{transaction}/payment', [TransactionController::class, 'recordPayment']);
            Route::post('/create', [TransactionController::class, 'create']);
        });

        // General routes accessible to authenticated users
        Route::get('/', [TransactionController::class, 'index']);
    });

    //logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
