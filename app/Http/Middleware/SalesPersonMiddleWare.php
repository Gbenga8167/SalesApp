<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            //if users is not logged in
        if(!Auth::check()){
            return redirect()->route('login');
        }

         $LoginUserRole = $request->user()->role;

     // Admin LOgin Role
     if( $LoginUserRole == 1 ){
          return redirect()->route('admin.dashboard');
     }

          // sales_person Login role
     if($LoginUserRole == 2){
         return redirect()->route('sales_person.dashboard');
      
     }

    }
}
