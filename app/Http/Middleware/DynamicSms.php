<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class DynamicSms
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
            $sms_permit = Auth::user()->userDetail->dynamic_permission;
            if ($sms_permit != 1) {
                return redirect('/');
            }
        }else {
            return redirect('/');
        }

        return $next($request);
    }
}
