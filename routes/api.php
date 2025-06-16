<?php

use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\WithdrawalController;
use App\Http\Middleware\AuditMiddleware;
use App\Http\Middleware\RateLimitMiddleware;
use Illuminate\Http\Request;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', AuditMiddleware::class, RateLimitMiddleware::class])
    ->prefix('v1')
    ->group(function () {

        Route::group(['prefix' => 'accounts'], function () {
            Route::get('/', [AccountController::class, 'index'])
                ->name('accounts.index');
            Route::post('/', [AccountController::class, 'store'])
                ->name('accounts.store');
            Route::get('/{account}', [AccountController::class, 'show'])
                ->name('accounts.show');

        });

        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/', [TransactionController::class, 'index'])
                ->name('transactions.index');
            Route::get('/{transaction}', [TransactionController::class, 'show'])
                ->name('transactions.show');
            Route::delete('/{transaction}', [TransactionController::class, 'destroy'])
                ->middleware([IsAdminMiddleware::class])
                ->name('transactions.destroy');
        });

        Route::post('/withdrawal', [WithdrawalController::class, 'withdraw'])
            ->name('withdrawal.perform');
    });
