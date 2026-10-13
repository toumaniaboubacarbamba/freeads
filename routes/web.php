<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdController::class, 'index'])->name('home');

Route::resource('ads', AdController::class)->except('index');
Route::get('/mes-annonces', [AdController::class, 'mine'])->name('ads.mine');

// Breeze redirige vers "dashboard" après connexion : on l'envoie sur mes annonces
Route::get('/dashboard', fn () => redirect()->route('ads.mine'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
