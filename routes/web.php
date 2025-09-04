<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


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

Route::get('/debug-env', function () {
   return response()->json([
        'APP_URL'     => config('app.url'),
        'ASSET_URL' => config('app.asset_url'),
        'SESSION_SECURE_COOKIE' => config('session.secure'),
        'SESSION_PATH' => config('session.path'),
        'SESSION_DOMAIN'  => config('session.domain'),
        'Current url'   => url()->current(),
        'TRUSTED_PROXIES'   => request()->getTrustedProxies()
    ]);
});

Route::get('/debug-products', function () {
    $info = [
        'database'         => DB::getDatabaseName(),
        'user'             => DB::selectOne('select current_user as u')->u ?? null,
        'schema'           => DB::selectOne('select current_schema as s')->s ?? null,
        'search_path'      => DB::selectOne('show search_path')->search_path ?? null,
        'products_table?'  => Schema::hasTable('products'),
        'products_count'   => null,
        'products_any_row' => null,
        'soft_deleted_cnt' => null,
        'raw_count'        => null,
    ];

    // raw count (bypasses Eloquent/scopes)
    try {
        $info['raw_count'] = DB::selectOne('select count(*)::int as c from products')->c;
    } catch (\Throwable $e) {
        $info['raw_count'] = 'ERR: ' . $e->getMessage();
    }

    // eloquent (with scopes)
    try {
        $info['products_count'] = Product::count();
        $info['soft_deleted_cnt'] = method_exists(Product::class, 'bootSoftDeletes')
            ? Product::withTrashed()->count() - Product::count()
            : 0;
        $info['products_any_row'] = Product::first();
    } catch (\Throwable $e) {
        $info['products_count'] = 'ERR: ' . $e->getMessage();
    }

    // connection URL seen by app (helpful when config is cached)
    $info['DATABASE_URL'] = env('DATABASE_URL');

    return response()->json($info);
});

Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
});

require __DIR__.'/auth.php';
