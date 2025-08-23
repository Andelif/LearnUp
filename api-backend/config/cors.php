<?php

return [
    // only your API endpoints
    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // exact origins you want to allow
    'allowed_origins' => [
        'http://localhost:5173',            // local dev (Vite)
        'https://learn-up-two.vercel.app',  // Vercel production
    ],

    // (optional) allow Vercel preview deployments for this project
    'allowed_origins_patterns' => [
        '#^https://.*\.vercel\.app$#',
    ],

    // be explicit so Authorization is definitely allowed
    'allowed_headers' => ['*'],   // includes Authorization, Content-Type, etc.
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, 
];
