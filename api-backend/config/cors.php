<?php

return [


    'paths' => ['api/*','sanctum/csrf-cookie', 'login', 'logout', 'user'],

    'allowed_methods' => ['*'],

    // 'allowed_origins' => [env('FRONTEND_URL','http://localhost:8000')],['http://localhost:5173'],
    'allowed_origins' => explode(',', env('CORS_ALLOW_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => (bool) env('CORS_ALLOW_CREDENTIALS', true),

];
