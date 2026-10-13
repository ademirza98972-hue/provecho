<?php

use App\Http\Controllers\CardRedirectController;
use App\Http\Controllers\Dashboard\CardController;
use App\Http\Controllers\Dashboard\ActivityController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ExportController;
use App\Http\Controllers\Dashboard\InvoiceController;
use App\Http\Controllers\Dashboard\MapController;
use App\Http\Controllers\Dashboard\PlacesController;
use App\Http\Controllers\Dashboard\ResellerController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\StatsController;
use Illuminate\Support\Facades\Route;

// Public — core NFC/QR endpoint + aktivasi mandiri oleh pemilik kartu
Route::get('/c/{id}', [CardRedirectController::class, 'redirect'])->name('card.redirect');
Route::post('/c/{id}/activate', [CardRedirectController::class, 'activate'])
    ->middleware('throttle:10,1')->name('card.activate');
Route::post('/c/{id}/buyer', [CardRedirectController::class, 'buyer'])
    ->middleware('throttle:10,1')->name('card.buyer');
Route::get('/nota/{invoice}', [InvoiceController::class, 'publicShow'])
    ->middleware(['signed', 'throttle:30,1'])->name('invoice.public');
Route::get('/nota/{invoice}/unduh', [InvoiceController::class, 'download'])
    ->middleware(['signed', 'throttle:30,1'])->name('invoice.download');
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
        Route::get('/buyer', [CardController::class, 'buyer'])->name('buyer');
        Route::put('/buyer', [CardController::class, 'updateBuyer'])->name('buyer.update');
    });

    // Fitur yang bisa diberikan per role (lihat Role::PERMISSIONS). Data user non-admin tetap terbatas ke card miliknya.
    Route::middleware('can:invoices')->group(function () {
        Route::get('/nota', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::match(['get', 'post'], '/nota/buat', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/nota', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/nota/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/nota/{invoice}/ubah', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/nota/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    });

    Route::middleware('can:export')->group(function () {
        Route::get('/export/cards', [ExportController::class, 'cards'])->name('stats.export');
        Route::get('/export/activity', [ExportController::class, 'activity'])->name('activity.export');
    });

    Route::get('/peta', [MapController::class, 'index'])->middleware('can:map')->name('map');

    Route::prefix('cards')->name('cards.')->group(function () {
        Route::match(['get', 'post'], '/export/pdf', [CardController::class, 'exportPdf'])->middleware('can:cards-print')->name('export.pdf');
        Route::post('/bulk-delete', [CardController::class, 'bulkDestroy'])->middleware('can:cards-delete')->name('bulk-destroy');
        Route::post('/{card}/disable', [CardController::class, 'disable'])->middleware(['can:manage-card,card', 'can:cards-disable'])->name('disable');
        Route::post('/{card}/reactivate', [CardController::class, 'reactivate'])->middleware(['can:manage-card,card', 'can:cards-disable'])->name('reactivate');
        Route::post('/{card}/reset', [CardController::class, 'reset'])->middleware(['can:manage-card,card', 'can:cards-reset'])->name('reset');
        Route::delete('/{card}', [CardController::class, 'destroy'])->middleware(['can:manage-card,card', 'can:cards-delete'])->name('destroy');
    });

    // Khusus admin utama: stok pusat dan pengelolaan pengguna/role.
    Route::middleware('can:admin')->group(function () {
        Route::post('/cards/generate', [CardController::class, 'generate'])->name('cards.generate');
        Route::post('/cards/assign', [CardController::class, 'assign'])->name('cards.assign');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::put('/resellers/{reseller}/role', [ResellerController::class, 'updateRole'])->name('resellers.role');

        Route::get('/resellers', [ResellerController::class, 'index'])->name('resellers.index');
        Route::post('/resellers', [ResellerController::class, 'store'])->name('resellers.store');
        Route::post('/resellers/assign-ids', [ResellerController::class, 'assignIds'])->name('resellers.assign-ids');
        Route::get('/resellers/{reseller}/brand', [ResellerController::class, 'brand'])->name('resellers.brand');
        Route::put('/resellers/{reseller}/brand', [ResellerController::class, 'updateBrand'])->name('resellers.brand.update');
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
Route::get('/produk', fn() => view('produk'))->name('produk');
