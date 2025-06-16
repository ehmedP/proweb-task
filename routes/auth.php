<?php

use App\Http\Controllers\API\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
|
| Here are the authentication routes for the ATM API application.
| These routes handle user authentication, registration, and profile management.
|
*/

// Public Authentication Routes (No authentication required)

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {

        Route::post('/login', [AuthController::class, 'login'])
            ->name('login');

        Route::post('/register', [AuthController::class, 'register'])
            ->name('register');

    });

    Route::group(['prefix' => 'auth', 'middleware' => 'auth:sanctum'], function () {

        Route::get('/profile', [AuthController::class, 'profile'])
            ->name('profile');

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::post('/logout-all', [AuthController::class, 'logoutAll'])
            ->name('logout-all');

        Route::post('/refresh', [AuthController::class, 'refresh'])
            ->name('refresh');

        Route::post('/change-password', [AuthController::class, 'changePassword'])
            ->name('change-password');

    });
});
