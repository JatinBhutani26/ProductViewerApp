<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         |-----------------------------------------------------------------------
         | Trust proxies so Laravel/Lumen will respect X-Forwarded-* headers.
         |-----------------------------------------------------------------------
         |
         | We add the App\Http\Middleware\TrustProxies middleware first so
         | forwarded headers are applied early (scheme/host) before other
         | middleware runs.
         |
         | Ensure the middleware class exists at app/Http/Middleware/TrustProxies.php
         | (use the compatible implementation).
         */

        /** @var mixed $middleware */ // <- tell the static analyser not to check methods
        $middleware->add(App\Http\Middleware\TrustProxies::class);

        // keep this closure open for any other global middleware you need
        // $middleware->add(App\Http\Middleware\SomeOtherMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
