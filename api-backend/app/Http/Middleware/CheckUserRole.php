<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has the required role
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        return $next($request);
    }
}
