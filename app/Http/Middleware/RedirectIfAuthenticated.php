<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {

            if(Auth::guard('employee')->check()) {
                return redirect()->route('employee.login');
            }
            elseif(Auth::check()){
                if(Auth::user()->role=='5'){
                    return redirect('/home');
                }
                elseif(Auth::user()->role=='1' || Auth::user()->role=='2' ||Auth::user()->role=='3'){
                    return redirect()->route('admin.index');
                }
                elseif(Auth::user()->role=='4'){
                    return redirect('/reseller');
                }
                else{
                    Auth::logout();
                    session()->flash('message', 'something went wrong with your account. please contact with admin');
                    return redirect()->back();
                }
            }
        }
        return $next($request);
    }
}
