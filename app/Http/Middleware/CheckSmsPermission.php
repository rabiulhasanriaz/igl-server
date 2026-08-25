<?php

namespace App\Http\Middleware;

use Closure;
use Auth;


class CheckSmsPermission
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
            $sms_permit = explode(',',Auth::user()->permission);
            if (!in_array(1,$sms_permit)) {
                return redirect('/');
            }
        }else {
            return redirect('/');
        }

        return $next($request);



    }
}
