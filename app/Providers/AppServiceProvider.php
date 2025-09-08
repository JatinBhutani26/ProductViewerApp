<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Priority for public root:
         * 1. Use APIM / other gateway headers if provided (recommended)
         *    - X-Forwarded-Host
         *    - X-Forwarded-Proto
         *    - X-Forwarded-Prefix
         * 2. Else use ASSET_URL
         * 3. Else use APP_URL
         *
         * This avoids hard-coding FORCE_ROOT_URL and keeps the image portable.
         */
        $root = null;

        // 1) Attempt to build root from forwarded headers (APIM)
        // Use $_SERVER directly because this runs early during boot and may run outside a request lifecycle.
        $xfHost   = !empty($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( !empty($_SERVER['X_FORWARDED_HOST']) ? $_SERVER['X_FORWARDED_HOST'] : null );
        $xfProto  = !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO'] : ( !empty($_SERVER['X_FORWARDED_PROTO']) ? $_SERVER['X_FORWARDED_PROTO'] : null );
        $xfPrefix = !empty($_SERVER['HTTP_X_FORWARDED_PREFIX']) ? $_SERVER['HTTP_X_FORWARDED_PREFIX'] : ( !empty($_SERVER['X_FORWARDED_PREFIX']) ? $_SERVER['X_FORWARDED_PREFIX'] : null );

        if ($xfHost) {
            // prefer the forwarded proto if present, else infer from SERVER_PORT / HTTPS, else default to https
            if ($xfProto) {
                // header might contain multiple comma-separated values (take first)
                $proto = explode(',', $xfProto)[0];
                $proto = trim($proto);
            } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                $proto = 'https';
            } elseif (!empty($_SERVER['SERVER_PORT']) && in_array((string) $_SERVER['SERVER_PORT'], ['443'])) {
                $proto = 'https';
            } else {
                $proto = 'http';
            }

            // forwarded host may include port; take the first host if comma separated
            $host = trim(explode(',', $xfHost)[0]);

            // normalize the prefix (if provided) to a single starting slash and no trailing slash
            if ($xfPrefix) {
                $prefix = '/' . ltrim(rtrim(explode(',', $xfPrefix)[0], '/'), '/');
            } else {
                $prefix = '';
            }

            $root = $proto . '://' . $host . ($prefix !== '' ? $prefix : '');
        }

        // 2) fallback to ASSET_URL then APP_URL from env
        if (empty($root)) {
            $root = env('ASSET_URL') ?: env('APP_URL') ?: null;
        }

        if ($root) {
            // Ensure no trailing slash (so URL::forceRootUrl behavior is consistent)
            $root = rtrim($root, '/');

            // Force the URL generator to use this root for url(), route(), action() helpers
            URL::forceRootUrl($root);

            // Force scheme (http/https) if it's part of the root URL
            $scheme = parse_url($root, PHP_URL_SCHEME);
            if ($scheme) {
                URL::forceScheme($scheme);
            }
        }
    }
}
