<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Trust proxies must come very early so forwarded headers are processed
        \App\Http\Middleware\TrustProxies::class,

        // default Laravel middleware you may already have (keep as-is)
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        // ... anything else in your $middleware before/after
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // your web middleware...
        ],

        'api' => [
            // your api middleware...
        ],
    ];

    /**
     * Route middleware.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        // your route middleware...
    ];
}
