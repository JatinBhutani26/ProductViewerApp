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
       $root = env('APP_URL') ?: env('ASSET_URL') ?: env('APP_URL');

        if ($root) {
            // Ensure no trailing slash
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
