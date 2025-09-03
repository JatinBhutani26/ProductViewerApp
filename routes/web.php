<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/debug-db', function () {
    $status = null;

    try {
        DB::connection()->getPdo();
        $status = '✅ Connection OK';
    } catch (\Exception $e) {
        $status = '❌ ' . $e->getMessage();
    }

    return response()->json([
        'DATABASE_URL' => env('DATABASE_URL'),
        'DB_HOST'      => env('DB_HOST'),
        'DB_DATABASE'  => env('DB_DATABASE'),
        'DB_USERNAME'  => env('DB_USERNAME'),
        'DB_SSLMODE'   => env('DB_SSLMODE'),
        'DB_CONFIG'    => Config::get('database.connections.pgsql'),
        'DB_CHECK'     => $status,
    ]);
});


Route::get('/products', [ProductController::class, 'index'])->middleware('auth');

require __DIR__.'/auth.php';
