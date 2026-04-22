<?php
// CORS configuration
// allowed origins read from .env - change CORS_ALLOWED_ORIGINS when front is connected
// no code change needed when switching environments

return [
    // routes to apply CORS headers to
    'paths' => ['api/*'],

    // allowed HTTP methods
    'allowed_methods' => ['*'],

    // origins allowed to call the API
    // .env: CORS_ALLOWED_ORIGINS=http://localhost:5173,https://myapp.com
    // '*' = allow all (dev only - restrict in production)
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    // no origin patterns needed - handled by allowed_origins
    'allowed_origins_patterns' => [],

    // allowed headers in requests
    'allowed_headers' => ['*'],

    // headers to expose to the browser
    'exposed_headers' => [],

    // preflight cache duration (seconds)
    'max_age' => 0,

    // false: Bearer token mode (no cookies) - set true only if switching to SPA cookie auth
    'supports_credentials' => false,
];
