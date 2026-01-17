<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
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

// Zones, Tables, Parametres, Users - Admin seulement (avec rate limiting)
Route::middleware(['auth', 'role:admin', 'throttle:60,1'])->group(function () {
    Route::resource('zones', ZoneController::class)->except(['show']);
    Route::resource('tables', TableController::class)->except(['show']);
    Route::patch('tables/{table}/statut', [TableController::class, 'updateStatut'])->name('tables.statut');

    // Parametres
    Route::get('parametres', [ParametreController::class, 'index'])->name('parametres.index');
    Route::put('parametres/general', [ParametreController::class, 'updateGeneral'])->name('parametres.update.general');
    Route::get('parametres/horaires', [ParametreController::class, 'horaires'])->name('parametres.horaires');
    Route::put('parametres/horaires', [ParametreController::class, 'updateHoraires'])->name('parametres.update.horaires');

    // Utilisateurs
    Route::resource('users', UserController::class)->except(['show']);
});

// Routes protégées par authentification (tous les users)
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Reservations (admin + reception)
    Route::get('reservations/search', [ReservationController::class, 'search'])->name('reservations.search');
    Route::resource('reservations', ReservationController::class)->except(['show']);
    Route::patch('reservations/{reservation}/statut', [ReservationController::class, 'updateStatut'])->name('reservations.statut');
});

require __DIR__.'/auth.php';
