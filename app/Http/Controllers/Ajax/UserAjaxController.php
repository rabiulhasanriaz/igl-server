<?php

namespace App\Http\Controllers\Ajax;

use App\Model\SmsCampaign;
use App\Model\SmsCampaign_24h;
use App\Model\SmsDesktop24h;
use App\Model\SmsDesktopCampaignId;
use App\Model\SmsDesktop;
use App\Model\SmsCampaignId;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserAjaxController extends Controller
{
    /*show todays sms reports*/
    public function showTodaysReportDetail(Request $request)
    {
        try{
            $reports = SmsCampaign_24h::with('sender', 'operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            if(count($reports)>0) {
                return view('user.ajax.views.todays_report', compact('reports'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }

    public function showTodaysDynamicReportDetail(Request $request)
    {
        try{
            $reports = SmsDesktop24h::with('operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            if(count($reports)>0) {
                return view('user.ajax.dynamic.ajax.views.todays_report', compact('reports'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }

    /*show todays sms reports*/
public function showArchivedReportDetail(Request $request)
{
    try {
        $campaign_id = $request->campaign_id;
        
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        $campaign = SmsCampaignId::where('id', $campaign_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$campaign) {
            return response()->json(['error' => 'Campaign not found'], 404);
        }
        
        // Get from 24h table
        $reports = SmsCampaign_24h::with('sender')
            ->where('user_id', Auth::id())
            ->where('campaign_id', $campaign_id)
            ->orderBy('id', 'desc')
            ->get();
        
        // If empty, try main table
        if ($reports->isEmpty()) {
            $reports = SmsCampaign::with('sender')
                ->where('user_id', Auth::id())
                ->where('campaign_id', $campaign_id)
                ->orderBy('id', 'desc')
                ->get();
        }
        
        return view('user.ajax.views.show_archived_report', compact('reports', 'campaign'));
        
    } catch (\Exception $e) {
        \Log::error('showArchivedReportDetail error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function showArchivedReportDetailDynamic(Request $request)
    {
        try{
            $campaign = SmsDesktopCampaignId::where('id',$request->campaign_id)->first();
            $reports = SmsDesktop::with('operator')->where(['user_id'=>Auth::id(), 'campaign_id'=>$request->campaign_id])->get();
            if(count($reports)>0) {
                return view('user.ajax.dynamic.ajax.views.archived_report', compact('reports','campaign'));
            }else{
                return "No Data Available";
            }
        }catch (\Exception $e){
            return "message:: ".$e->getMessage();
        }
    }


}
