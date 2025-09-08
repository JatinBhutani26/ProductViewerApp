<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| Trusted proxies configuration
|--------------------------------------------------------------------------
|
| Allows configuration via environment variables:
|  - TRUSTED_PROXIES (comma separated IPs or "*" for all)
|  - TRUSTED_HEADERS (one of: "FORWARDED", "X_FORWARDED_ALL", default "X_FORWARDED_ALL")
|
| We avoid direct static references to Request::HEADER_* constants so the
| static analyzer (Intelephense) doesn't complain and so code runs across
| different Symfony versions.
|
*/

$trustedProxiesEnv = env('TRUSTED_PROXIES', '*');

if ($trustedProxiesEnv === '*' || trim($trustedProxiesEnv) === '') {
    // Trust all proxies
    $trustedProxies = ['0.0.0.0/0'];
} else {
    $trustedProxies = array_map('trim', explode(',', $trustedProxiesEnv));
}

// Normalize header env name
$trustedHeadersEnv = strtoupper(trim(env('TRUSTED_HEADERS', 'X_FORWARDED_ALL')));

// Helper to safely check and read a class constant by name
$constName = function (string $class, string $constant) {
    $full = $class . '::' . $constant;
    return defined($full) ? constant($full) : null;
};

// Build header flags using available constants (safe)
$header_forwarded = $constName(Request::class, 'HEADER_FORWARDED');
$header_x_forwarded_for = $constName(Request::class, 'HEADER_X_FORWARDED_FOR');
$header_x_forwarded_host = $constName(Request::class, 'HEADER_X_FORWARDED_HOST');
$header_x_forwarded_port = $constName(Request::class, 'HEADER_X_FORWARDED_PORT');
$header_x_forwarded_proto = $constName(Request::class, 'HEADER_X_FORWARDED_PROTO');
$header_x_forwarded_all = $constName(Request::class, 'HEADER_X_FORWARDED_ALL');

// Compose a fallback bitmask for x-forwarded-all if not present
$composed_x_forwarded_all = 0;
foreach ([
    $header_x_forwarded_for,
    $header_x_forwarded_host,
    $header_x_forwarded_port,
    $header_x_forwarded_proto,
] as $h) {
    if (is_int($h)) {
        $composed_x_forwarded_all |= $h;
    }
}

// Decide final header flag value
if ($trustedHeadersEnv === 'FORWARDED' && is_int($header_forwarded)) {
    $trustedHeaders = $header_forwarded;
} elseif ($trustedHeadersEnv === 'X_FORWARDED_ALL') {
    // Prefer the explicit constant if available, otherwise use composed fallback
    $trustedHeaders = is_int($header_x_forwarded_all) ? $header_x_forwarded_all : $composed_x_forwarded_all;
} else {
    // Any other value: try to resolve to X_FORWARDED_ALL fallback
    $trustedHeaders = is_int($header_x_forwarded_all) ? $header_x_forwarded_all : $composed_x_forwarded_all;
}

// If still zero / null, default to trusting FORWARDED plus X-Forwarded-For variety (best-effort)
if (empty($trustedHeaders) && is_int($header_forwarded)) {
    $trustedHeaders = $header_forwarded;
} elseif (empty($trustedHeaders)) {
    // Prevent passing null/false to setTrustedProxies; use 0 which will be ignored
    $trustedHeaders = 0;
}

// Finally apply trusted proxies/headers (Symfony Request API)
try {
    // Request::setTrustedProxies expects array|string and int (flags)
    Request::setTrustedProxies($trustedProxies, $trustedHeaders);
} catch (Throwable $e) {
    // If something goes wrong, we still continue — don't break the boot sequence.
    // You can log here if desired (but bootstrap is early; avoid heavy I/O).
}

/*
|--------------------------------------------------------------------------
| Build the application
|--------------------------------------------------------------------------
*/
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
