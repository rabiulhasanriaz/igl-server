<?php

namespace App\Http\Controllers\Reseller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LoadCampaign30day;
use App\Model\SmsCampaign_24h;
use App\Model\User;
use App\Model\SmsCampaign;
use Auth;
use DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
        public function sms_flexi_reports(){
            $create = Auth::user()->id;
            $user = User::where('create_by',Auth::user()->id)->pluck('id');
            
            $sms_user = SmsCampaign_24h::whereIn('user_id',$user)
                                        ->groupBy('user_id')
                                        ->get();
            
            $flexi_user = LoadCampaign30day::whereIn('user_id',$user)
                                        ->groupBy('user_id')
                                        ->where('created_at','>=', Carbon::now()->subDay())
                                        ->get();
            return view('reseller.reports.sms_flexi_reports',compact('sms_user','flexi_user'));
        }
        

}
