<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| Trusted proxies + forwarded headers
|--------------------------------------------------------------------------
|
| - Reads TRUSTED_PROXIES (comma separated or "*") and TRUSTED_HEADERS env vars.
| - Calls Request::setTrustedProxies early to allow Symfony to use X-Forwarded-*.
| - As a fallback, if APIM has set X-Forwarded-Host / X-Forwarded-Proto we
|   override $_SERVER values so URL generation uses the APIM host/scheme.
|
| Keep this minimal and safe for static analyzers (avoid direct undefined constants).
|
*/

$trustedProxiesEnv = env('TRUSTED_PROXIES', '*');
$trustedHeadersEnv = strtoupper(trim(env('TRUSTED_HEADERS', 'X_FORWARDED_ALL')));

// Build trusted proxies array
if ($trustedProxiesEnv === '*' || trim($trustedProxiesEnv) === '') {
    $trustedProxies = ['0.0.0.0/0'];
} else {
    $trustedProxies = array_filter(array_map('trim', explode(',', $trustedProxiesEnv)));
}

// Helper to read a class constant if it exists
$const = function (string $class, string $name) {
    $fullname = $class . '::' . $name;
    return defined($fullname) ? constant($fullname) : null;
};

// Resolve Symfony Request header flags safely
$h_forwarded = $const(Request::class, 'HEADER_FORWARDED');
$h_x_forw_for = $const(Request::class, 'HEADER_X_FORWARDED_FOR');
$h_x_forw_host = $const(Request::class, 'HEADER_X_FORWARDED_HOST');
$h_x_forw_port = $const(Request::class, 'HEADER_X_FORWARDED_PORT');
$h_x_forw_proto = $const(Request::class, 'HEADER_X_FORWARDED_PROTO');
$h_x_forw_all = $const(Request::class, 'HEADER_X_FORWARDED_ALL');

// Compose an "X_FORWARDED_ALL" fallback if the constant is not available
$composed_x_all = 0;
foreach ([$h_x_forw_for, $h_x_forw_host, $h_x_forw_port, $h_x_forw_proto] as $v) {
    if (is_int($v)) {
        $composed_x_all |= $v;
    }
}

// Decide header flags per env
if ($trustedHeadersEnv === 'FORWARDED' && is_int($h_forwarded)) {
    $trustedHeaders = $h_forwarded;
} elseif ($trustedHeadersEnv === 'X_FORWARDED_ALL') {
    $trustedHeaders = is_int($h_x_forw_all) ? $h_x_forw_all : $composed_x_all;
} else {
    // default fallback: use X_FORWARDED_ALL (explicit or composed)
    $trustedHeaders = is_int($h_x_forw_all) ? $h_x_forw_all : $composed_x_all;
}

// Ensure $trustedHeaders is an int (or 0)
if (!is_int($trustedHeaders)) {
    $trustedHeaders = 0;
}

// Apply trusted proxies (do not fatal if something goes wrong)
try {
    // Note: Request::setTrustedProxies accepts array|string and int flags
    Request::setTrustedProxies($trustedProxies, $trustedHeaders);
} catch (Throwable $e) {
    // swallow: bootstrap should not throw hard errors here
}

/*
|--------------------------------------------------------------------------
| Fallback: ensure PHP server variables reflect forwarded Host / Proto
|--------------------------------------------------------------------------
|
| Some frameworks or URL generation may read $_SERVER before Symfony rewrites
| them. As a last-resort, if APIM set X-Forwarded-Host / X-Forwarded-Proto,
| override $_SERVER so url() and asset() functions produce APIM URLs.
|
*/
$xfh = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_X_FORWARDED_HOST'] ?? null;
$xfp = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;

if (!empty($xfh)) {
    // If there are multiple hosts (comma list), take the first (left-most) per RFC
    $firstHost = trim(explode(',', $xfh)[0]);
    if ($firstHost !== '') {
        $_SERVER['HTTP_HOST'] = $firstHost;
        // also update SERVER_NAME
        $_SERVER['SERVER_NAME'] = $firstHost;
    }
}

if (!empty($xfp)) {
    $proto = strtolower(trim(explode(',', $xfp)[0]));
    if ($proto === 'https') {
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = '443';
    } elseif ($proto === 'http') {
        unset($_SERVER['HTTPS']);
        $_SERVER['SERVER_PORT'] = '80';
    }
}

/*
|--------------------------------------------------------------------------
| Build the application (keep your app structure unchanged)
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
