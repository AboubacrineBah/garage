<?php

/**
 * Routes d'authentification Garage Moderne.
 *
 * Dans routes/web.php :
 *   require __DIR__.'/auth.php';
 *   require __DIR__.'/web_garage.php';
 */

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Invités uniquement
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Utilisateurs connectés
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
