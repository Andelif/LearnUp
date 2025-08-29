<?php

return [

    // Only your API routes (adjust if you also hit /login outside /api)
    'paths' => ['api/*', 'login', 'register'],

    'allowed_methods' => ['*'],

    // Put your exact origins here
    'allowed_origins' => explode(',', env('CORS_ALLOW_ORIGINS',
        'http://localhost:5173,https://learn-up-two.vercel.app'
    )),

    'allowed_origins_patterns' => [],

    // Allow standard headers + Authorization for tokens
    'allowed_headers' => ['*'],

    // (Optional) expose Authorization for debugging in dev
    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    // IMPORTANT for token mode: no cookies => false
    'supports_credentials' => false,

];
