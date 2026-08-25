<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class CheckFlexiPermission
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
            $flexi_permit = explode(',',Auth::user()->permission);
            if (!in_array(2,$flexi_permit)) {
                return redirect('/');
            }
        }else {
            return redirect('/');
        }

        return $next($request);



    }
}
