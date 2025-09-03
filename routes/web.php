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
    $dbCheck = '';
    $queryResult = null;

    try {
        // Try connecting to DB
        $pdo = DB::connection()->getPdo();
        $dbCheck = '✅ Connection OK';

        // Run a simple test query
        $queryResult = DB::select('select 1 as test');
    } catch (\Exception $e) {
        $dbCheck = '❌ ' . $e->getMessage();
    }

    return response()->json([
        'DB_HOST'     => config('database.connections.pgsql.host'),
        'DB_DATABASE' => config('database.connections.pgsql.database'),
        'DB_USERNAME' => config('database.connections.pgsql.username'),
        'DB_PASSWORD' => config('database.connections.pgsql.password'),
        'DB_SSLMODE'  => config('database.connections.pgsql.sslmode'),
        'DB_CONFIG'   => Config::get('database.connections.pgsql'),
        'DB_CHECK'    => $dbCheck,
        'TEST_QUERY'  => $queryResult,
    ]);
});


Route::get('/products', [ProductController::class, 'index'])->middleware('auth');

require __DIR__.'/auth.php';
