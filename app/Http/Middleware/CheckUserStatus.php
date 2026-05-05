<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status == 0) {

            Auth::logout();

            return redirect()->route('login')->withErrors([
                'login' => 'Your account has been deactivated.'
            ]);
        }

        return $next($request);
    }
}
