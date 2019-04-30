<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (Auth::guest()) {
            return redirect(route('login'));
        }
    
        if (!Auth::user()->is_admin) {
            return redirect('/')->with('message','Access denied.');
        }

        return $next($request);
    }
}
