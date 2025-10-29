<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Product Stage Tracker Routes
Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::put('/products/{product}/basic', [ProductController::class, 'updateBasic'])->name('products.updateBasic');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::post('/products/{product}/submit', [ProductController::class, 'submit'])->name('products.submit');
Route::get('/submitted', [ProductController::class, 'submitted'])->name('products.submitted');
Route::get('/export-csv', [ProductController::class, 'exportCsv'])->name('products.export');
// Daily documents list (created today)
Route::get('/daily', [ProductController::class, 'daily'])->name('products.daily');