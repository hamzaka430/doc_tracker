<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SapErrorController;
use App\Http\Controllers\InjectionDetailController;
use App\Http\Controllers\ChatController;
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

    // Vials Data / Injection Details routes
    Route::get('/vials-data/export', [InjectionDetailController::class, 'exportCsv'])->name('injection_details.export');
    Route::get('/vials-data/daily', [InjectionDetailController::class, 'daily'])->name('injection_details.daily');
    Route::resource('vials-data', InjectionDetailController::class)->names([
        'index' => 'injection_details.index',
        'create' => 'injection_details.create',
        'store' => 'injection_details.store',
        'edit' => 'injection_details.edit',
        'update' => 'injection_details.update',
        'destroy' => 'injection_details.destroy',
    ]);
    // User Management routes
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/permissions', [\App\Http\Controllers\UserController::class, 'updatePermissions'])->name('users.updatePermissions');

    // Secret Chat Support routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/setup-pin', [ChatController::class, 'setupPin'])->name('chat.setupPin');
    Route::post('/chat/verify-pin', [ChatController::class, 'verifyPin'])->name('chat.verifyPin');
    Route::post('/chat/lock', [ChatController::class, 'lock'])->name('chat.lock');
    Route::post('/chat/messages', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/react', [ChatController::class, 'reactMessage'])->name('chat.react');
    Route::post('/chat/edit', [ChatController::class, 'editMessage'])->name('chat.edit');
    Route::delete('/chat/delete/{id}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
    Route::post('/chat/pin', [ChatController::class, 'togglePin'])->name('chat.pin');
    Route::post('/chat/preferences', [ChatController::class, 'updatePreferences'])->name('chat.preferences');
    Route::post('/chat/settings/names', [ChatController::class, 'updateNames'])->name('chat.updateNames');
    Route::delete('/chat/messages/clear', [ChatController::class, 'clearChat'])->name('chat.clear');

});

/*
|--------------------------------------------------------------------------
| Chat Poll Route (No auth middleware)
|--------------------------------------------------------------------------
| This route is polled every 1.5 s. Removing the auth middleware eliminates
| 1 DB query per request (User::find from users table on JawsDB).
| Security is handled inside the controller via session('chat_unlocked').
*/
Route::get('/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unreadCount');

/*
|--------------------------------------------------------------------------
| Chat DB Keep-Alive Heartbeat Route
|--------------------------------------------------------------------------
| Pinged periodically by GitHub Actions workflow to prevent the Aiven
| cloud database from sleeping / powering off due to inactivity.
*/
Route::get('/api/chat-db-keepalive', function () {
    try {
        $start = microtime(true);
        \Illuminate\Support\Facades\DB::connection('mysql_chat')->select('SELECT 1 as ping');
        $duration = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => 'success',
            'message' => 'Chat database is active and connected',
            'latency_ms' => $duration,
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Chat database ping failed: ' . $e->getMessage(),
        ], 500);
    }
})->name('chat.keepalive');

require __DIR__.'/auth.php';
