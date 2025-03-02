<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoginStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !session('is_logged_in')) {
            // Update session if user is authenticated but session is missing
            session([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_email' => Auth::user()->email,
                'last_login' => now(),
                'is_logged_in' => true
            ]);
        }

        return $next($request);
    }
} 