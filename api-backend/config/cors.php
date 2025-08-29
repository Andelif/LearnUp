<?php

return [
    
    'paths' => ['api/*', 'login', 'register'],

    'allowed_methods' => ['*'],

    
    'allowed_origins' => explode(',', env('CORS_ALLOW_ORIGINS',
        'http://localhost:5173,https://learn-up-two.vercel.app'
    )),

    'allowed_origins_patterns' => [],

    
    'allowed_headers' => ['*'],

    
    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    
    'supports_credentials' => false,
];
