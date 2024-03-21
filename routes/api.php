<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsVerificator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardIzinController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DashboardVerificatorController;
use App\Http\Controllers\IzinController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Models\Izin;

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


Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::prefix('/dashboard')->group(function () {
        // Admin
        Route::middleware(IsAdmin::class)->group(function () {
            Route::prefix('/users')->group(function () {
                Route::get('/', [DashboardUserController::class, 'index']);
                Route::post('/{id}/add-verificator', [DashboardUserController::class, 'addVerificator']);
                Route::post('/{id}/reset-password', [DashboardUserController::class, 'resetPassword']);
            });
            Route::prefix('/izin')->group(function () {
                Route::get('/', [DashboardIzinController::class, 'index']);
            });
            Route::prefix('/verificators')->group(function () {
                Route::post('/', [DashboardVerificatorController::class, 'store']);
            });
        });

        // Verificator
        Route::middleware(IsVerificator::class)->group(function () {
            Route::prefix('/izin')->group(function () {
                Route::post('/{id}/action', [DashboardIzinController::class, 'actionIzin']);
            });
            Route::post('/users/{id}/verify', [DashboardVerificatorController::class, 'verifyUser']);
        });
    });

    // User
    Route::prefix('/izin')->group(function () {
        Route::get('/', [IzinController::class, 'index']);
        Route::post('/', [IzinController::class, 'create']);
        Route::get('/{id}', [IzinController::class, 'show']);
        Route::put('/{id}', [IzinController::class, 'update']);
        Route::delete('/{id}/cancel', [IzinController::class, 'cancel']);
        Route::delete('/{id}', [IzinController::class, 'destroy']);
    });

    Route::prefix('/auth')->group(function () {
        Route::post('/update-password', [AuthController::class, 'updatePassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});

Route::middleware('guest')->group(function () {
    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
});
