<?php

use App\Http\Controllers\CardRedirectController;
use App\Http\Controllers\Dashboard\CardController;
use App\Http\Controllers\Dashboard\ActivityController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ExportController;
use App\Http\Controllers\Dashboard\PlacesController;
use App\Http\Controllers\Dashboard\StatsController;
use Illuminate\Support\Facades\Route;

// Public — core NFC/QR endpoint + aktivasi mandiri oleh pemilik kartu
Route::get('/c/{id}', [CardRedirectController::class, 'redirect'])->name('card.redirect');
Route::post('/c/{id}/activate', [CardRedirectController::class, 'activate'])
    ->middleware('throttle:10,1')->name('card.activate');
Route::get('/c/{id}/places', [CardRedirectController::class, 'places'])
    ->middleware('throttle:20,1')->name('card.places');
Route::post('/c/{id}/resolve-maps', [CardRedirectController::class, 'resolveMaps'])
    ->middleware('throttle:15,1')->name('card.resolve-maps');

// Auth
Route::get('/login', fn() => view('auth.login'))->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Dashboard — protected
Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');

    // Export
    Route::get('/export/cards', [ExportController::class, 'cards'])->name('stats.export');
    Route::get('/export/activity', [ExportController::class, 'activity'])->name('activity.export');

    Route::prefix('cards')->name('cards.')->group(function () {
        Route::get('/', [CardController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/export/pdf', [CardController::class, 'exportPdf'])->name('export.pdf');
        Route::post('/generate', [CardController::class, 'generate'])->name('generate');
        Route::get('/{card}', [CardController::class, 'show'])->name('show');
        Route::post('/{card}/activate', [CardController::class, 'activate'])->name('activate');
        Route::put('/{card}/update', [CardController::class, 'update'])->name('update');
        Route::post('/{card}/disable', [CardController::class, 'disable'])->name('disable');
        Route::post('/{card}/reactivate', [CardController::class, 'reactivate'])->name('reactivate');
        Route::post('/{card}/reset', [CardController::class, 'reset'])->name('reset');
        Route::delete('/{card}', [CardController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [CardController::class, 'bulkDestroy'])->name('bulk-destroy');
    });

    Route::get('/places/search', [PlacesController::class, 'search'])->name('places.search');
    Route::post('/places/resolve-maps', [PlacesController::class, 'resolveMaps'])->name('places.resolve-maps');
});

Route::get('/', fn() => view('landing'))->name('landing');
