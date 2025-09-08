<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;

/**
 * Minimal TrustProxies middleware that avoids static-analyser complaints
 * by using integer constants (the same values Symfony uses for the
 * HEADER_* constants).
 *
 * Symfony Request constants mapping (for reference):
 *  - HEADER_FORWARDED           = 1
 *  - HEADER_X_FORWARDED_FOR     = 2
 *  - HEADER_X_FORWARDED_HOST    = 4
 *  - HEADER_X_FORWARDED_PROTO   = 8
 *  - HEADER_X_FORWARDED_PORT    = 16
 *  - HEADER_X_FORWARDED_ALL     = 2 | 4 | 8 | 16 = 30
 *
 * We use integers directly so Intelephense can't complain about missing
 * symbols while runtime behavior remains correct.
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Accept '*' to trust all proxies, or a comma-separated list of IPs/CIDRs.
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * The headers mask that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers;

    public function __construct()
    {
        // Read env var at runtime. Default to '*' (trust all) if set that way.
        $env = env('TRUSTED_PROXIES', '*');

        if ($env === '*' || trim($env) === '') {
            $this->proxies = '*';
        } else {
            $this->proxies = array_map('trim', explode(',', $env));
        }

        // Determine headers mask using integers to avoid analyser warnings.
        // Default: x-forwarded-all -> 2|4|8|16 = 30
        $headersEnv = strtolower(env('TRUSTED_HEADERS', 'x-forwarded-all'));

        switch ($headersEnv) {
            case 'forwarded':
                $this->headers = 1; // HEADER_FORWARDED
                break;
            case 'x-forwarded-for':
                $this->headers = 2; // HEADER_X_FORWARDED_FOR
                break;
            case 'x-forwarded-host':
                $this->headers = 4; // HEADER_X_FORWARDED_HOST
                break;
            case 'x-forwarded-proto':
                $this->headers = 8; // HEADER_X_FORWARDED_PROTO
                break;
            case 'x-forwarded-port':
                $this->headers = 16; // HEADER_X_FORWARDED_PORT
                break;
            case 'x-forwarded-all':
            default:
                // HEADER_X_FORWARDED_ALL = 2|4|8|16 = 30
                $this->headers = 30;
                break;
        }

        parent::__construct();
    }
}
