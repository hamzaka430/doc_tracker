<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
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
    
    // Product routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/pending', [ProductController::class, 'pending'])->name('products.pending');
    Route::get('/products/submitted', [ProductController::class, 'submitted'])->name('products.submitted');
    Route::get('/products/daily', [ProductController::class, 'daily'])->name('products.daily');
    Route::get('/products/export', [ProductController::class, 'exportCsv'])->name('products.export');
    Route::get('/products/daily/pdf', [ProductController::class, 'exportDailyPdf'])->name('products.daily.pdf');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}/basic', [ProductController::class, 'updateBasic'])->name('products.updateBasic');
    Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/{product}/submit', [ProductController::class, 'submit'])->name('products.submit');
    Route::patch('/products/{product}/submission-date', [ProductController::class, 'updateSubmissionDate'])->name('products.updateSubmissionDate');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

require __DIR__.'/auth.php';
