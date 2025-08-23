<?php

// IMPORTANT: config files must ONLY return an array.
// Do not open database connections here.

return [
    'host'     => env('DB_HOST', '127.0.0.1'),
    'database' => env('DB_DATABASE', 'learnup_db'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
];
