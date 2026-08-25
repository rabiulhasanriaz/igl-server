<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //
    public function index()
    {
        if(Auth::check()){
            
            // Activate the login status 
            $user = Auth::user();
            $user->login_status = 1;
            $user->save();

            if((Auth::user()->role=='1') || (Auth::user()->role=='2') || (Auth::user()->role=='2')){
                return redirect('/root');
            }
            elseif(Auth::user()->role=='4'){
                return redirect('/reseller');
            }
            elseif(Auth::user()->role=='5'){
                return redirect('/');
            }
        }


        return redirect('/login');
    }


}
