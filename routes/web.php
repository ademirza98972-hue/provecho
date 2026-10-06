<?php

use App\Http\Controllers\CardRedirectController;
use App\Http\Controllers\Dashboard\CardController;
use App\Http\Controllers\Dashboard\ActivityController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ExportController;
use App\Http\Controllers\Dashboard\PlacesController;
use App\Http\Controllers\Dashboard\ResellerController;
use App\Http\Controllers\Dashboard\SettingsController;
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

    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');

    // Reseller hanya boleh membuka, mengaktifkan, dan mengubah card miliknya.
    Route::prefix('cards/{card}')->name('cards.')->middleware('can:manage-card,card')->group(function () {
        Route::get('/', [CardController::class, 'show'])->name('show');
        Route::post('/activate', [CardController::class, 'activate'])->name('activate');
        Route::put('/update', [CardController::class, 'update'])->name('update');
    });

    Route::middleware('can:admin')->group(function () {
        Route::get('/export/cards', [ExportController::class, 'cards'])->name('stats.export');
        Route::get('/export/activity', [ExportController::class, 'activity'])->name('activity.export');

        Route::prefix('cards')->name('cards.')->group(function () {
            Route::match(['get', 'post'], '/export/pdf', [CardController::class, 'exportPdf'])->name('export.pdf');
            Route::post('/generate', [CardController::class, 'generate'])->name('generate');
            Route::post('/bulk-delete', [CardController::class, 'bulkDestroy'])->name('bulk-destroy');
            Route::post('/assign', [CardController::class, 'assign'])->name('assign');
            Route::post('/{card}/disable', [CardController::class, 'disable'])->name('disable');
            Route::post('/{card}/reactivate', [CardController::class, 'reactivate'])->name('reactivate');
            Route::post('/{card}/reset', [CardController::class, 'reset'])->name('reset');
            Route::delete('/{card}', [CardController::class, 'destroy'])->name('destroy');
        });

        Route::get('/resellers', [ResellerController::class, 'index'])->name('resellers.index');
        Route::post('/resellers', [ResellerController::class, 'store'])->name('resellers.store');
        Route::post('/resellers/assign-ids', [ResellerController::class, 'assignIds'])->name('resellers.assign-ids');
        Route::put('/resellers/{reseller}/password', [ResellerController::class, 'password'])->name('resellers.password');
        Route::delete('/resellers/{reseller}', [ResellerController::class, 'destroy'])->name('resellers.destroy');
    });

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/brand', [SettingsController::class, 'updateBrand'])->name('settings.brand');

    Route::get('/places/search', [PlacesController::class, 'search'])->name('places.search');
    Route::post('/places/resolve-maps', [PlacesController::class, 'resolveMaps'])->name('places.resolve-maps');
});

Route::get('/', fn() => view('landing'))->name('landing');
