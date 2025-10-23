<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // If request expects JSON (API), return null to trigger 401
        if ($request->expectsJson()) {
            return null;
        }

        // Otherwise redirect to login page
        return route('login');
    }
}