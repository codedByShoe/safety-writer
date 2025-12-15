<?php

use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ObservationController;
use App\Models\Package;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    $packages = Package::all();

    return Inertia::render('Home', [
        'creditPackages' => $packages,
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('admin/packages', PackageController::class)->names([
        'index' => 'admin.packages.index',
        'create' => 'admin.packages.create',
        'store' => 'admin.packages.store',
        'edit' => 'admin.packages.edit',
        'update' => 'admin.packages.update',
        'destroy' => 'admin.packages.destroy',
    ])->except(['show']);

    Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('checkout/{package}', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::get('checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::resource('observation', ObservationController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy'])
        ->names(['index' => 'observation', 'store' => 'observation.store', 'show' => 'observation.show', 'update' => 'observation.update', 'destroy' => 'observation.destroy']);
});

Route::post('webhook', [CheckoutController::class, 'webhook'])->name('webhook');

require __DIR__ . '/settings.php';
