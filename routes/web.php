<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil - redirige vers login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard - Admin seulement
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard');

// Zones - Admin seulement (avec rate limiting)
Route::middleware(['auth', 'role:admin', 'throttle:60,1'])->group(function () {
    Route::resource('zones', ZoneController::class)->except(['show']);
});

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Réservations (temporaire - sera remplacé par le vrai controller)
    Route::get('/reservations', function () {
        return view('reservations.index');
    })->name('reservations.index');
});

require __DIR__.'/auth.php';
