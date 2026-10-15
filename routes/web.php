<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;

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

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Disallow: /profile',
        'Disallow: /mes-annonces',
        'Disallow: /ads/create',
        'Disallow: /ads/*/edit',
        'Disallow: /login',
        'Disallow: /register',
        'Disallow: /forgot-password',
        'Disallow: /reset-password',
        'Disallow: /verify-email',
        'Disallow: /confirm-password',
        '',
        'Sitemap: '.route('sitemap'),
    ];

    return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
});

require __DIR__.'/auth.php';
