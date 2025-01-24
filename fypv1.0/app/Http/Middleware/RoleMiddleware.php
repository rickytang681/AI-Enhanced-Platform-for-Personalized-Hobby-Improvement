<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Check if the user is authenticated and has the correct role
        if (auth()->check() && auth()->user()->role === $role) {
            return $next($request);
        }

        // If the user does not have the required role, abort with a 403
        abort(403, 'Unauthorized action.');
    }

    
}
