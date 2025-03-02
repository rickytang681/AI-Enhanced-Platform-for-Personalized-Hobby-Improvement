<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserSession
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in but session is missing
        if (Auth::check() && !session('is_logged_in')) {
            $user = Auth::user();
            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'last_login' => now(),
                'is_logged_in' => true
            ]);
        }
        
        // Check if session exists but user is not logged in
        if (!Auth::check() && session('is_logged_in')) {
            session()->flush();
            return redirect('/login');
        }

        return $next($request);
    }
} 