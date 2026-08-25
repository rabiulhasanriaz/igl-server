<?php

namespace App\Http\Controllers\User;

use App\Model\AccSmsRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
    //
    public function index(){
        $smsRates = AccSmsRate::with('country','user','operator')->where('user_id', Auth::id())->get();

    	return view('user.price.price_list', compact('smsRates'));
    }

    public function dynamic(){
        $smsRates = AccSmsRate::with('country','user','operator')->where('user_id', Auth::id())->get();

    	return view('user.price.price_dynamic', compact('smsRates'));
    }
}
