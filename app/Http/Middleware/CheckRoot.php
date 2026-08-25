<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRoot
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

        if(Auth::check()) {
            if ((Auth::user()->role == 3) || (Auth::user()->role == 2) || (Auth::user()->role == 1)) {
                return $next($request);
            }
        }
        return redirect('/home');

    }
}
