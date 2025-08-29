<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Define the allowed origins
        $allowedOrigins = ['http://localhost:5173', 'http://localhost:5174']; 

        // Get the origin from the request headers
        $origin = $request->headers->get('Origin');
        
        // If the origin is in the allowed list, set the header
        if (in_array($origin, $allowedOrigins)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            // Optionally, you can set a fallback behavior for non-allowed origins (e.g., 403 Forbidden)
            $response->headers->set('Access-Control-Allow-Origin', ''); // Or you can return an error response
        }

        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
