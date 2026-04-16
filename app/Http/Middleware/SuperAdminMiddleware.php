<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          // Check if super admin session exists
        if (!session()->has('super_admin')) {
            return redirect()->route('superadmin.login')
                ->withErrors(['Unauthorized access.']);
        }

        // Check if session expired
        if (now()->greaterThan(session('super_admin_expires_at'))) {

            // Destroy session if expired
            session()->forget([
                'super_admin',
                'super_admin_expires_at'
            ]);

            return redirect()->route('superadmin.login')
                ->withErrors(['Session expired. Please login again.']);
        }

        return $next($request);
    }
}
