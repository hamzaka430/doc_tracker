<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SapErrorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard route
    Route::get('/dashboard', function () {
        return redirect()->route('products.index');
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/pending', [ProductController::class, 'pending'])->name('products.pending');
    Route::get('/products/submitted', [ProductController::class, 'submitted'])->name('products.submitted');
    Route::get('/products/daily', [ProductController::class, 'daily'])->name('products.daily');
    Route::get('/products/export', [ProductController::class, 'exportCsv'])->name('products.export');
    Route::get('/products/daily/pdf', [ProductController::class, 'exportDailyPdf'])->name('products.daily.pdf');
    Route::get('/products/trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore')->withTrashed();
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::match(['PUT', 'PATCH'], '/products/{product}/basic', [ProductController::class, 'updateBasic'])->name('products.updateBasic');
    Route::match(['PUT', 'PATCH'], '/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/bulk-submit', [ProductController::class, 'bulkSubmit'])->name('products.bulkSubmit');
    Route::post('/products/{product}/submit', [ProductController::class, 'submit'])->name('products.submit');
    Route::match(['PUT', 'PATCH'], '/products/{product}/submission-date', [ProductController::class, 'updateSubmissionDate'])->name('products.updateSubmissionDate');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Preferences routes
    Route::post('/preferences', [\App\Http\Controllers\UserPreferenceController::class, 'update'])->name('preferences.update');

    // SAP Error routes
    Route::resource('sap-errors', SapErrorController::class);
});

require __DIR__.'/auth.php';
