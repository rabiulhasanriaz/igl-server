<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use PDF;
use App\Model\SmsCampaignId;
use App\Model\SmsDesktopCampaignId;

class CampaignReportController extends Controller
{
    public function campaignReport(Request $request){
        $start_date = Carbon::today()->format('Y-m-d');
        $end_date = $start_date;
        $title = trim((string) $request->camp_title);
        $q_start_date = Carbon::today();
        $q_end_date = Carbon::tomorrow();
    // $emp_id = Auth::guard('employee')->id();
    // $emp_create = EmployeeUser::where('id',$emp_id)->first();
    // $users_list = User::where('employee_user_id',$emp_id)->where('status',1)->get();
    // // dd($users);
    // dd(Auth::id());
    // // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();
    // // dd($users);
    
        $transactions = SmsCampaignId::with(['creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            }])
            ->select('id', 'user_id', 'sci_campaign_id', 'sci_campaign_title', 'sci_total_submitted', 'sci_total_cost', 'sci_targeted_time', 'sci_from_api')
            ->where('user_id', Auth::id())
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<', $q_end_date)
            ->when($title !== '', function ($query) use ($title) {
                $query->where('sci_campaign_title', 'like', "%{$title}%");
            })
            ->orderBy('sci_targeted_time', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->sci_targeted_time)->format('d M Y');
            });
    // dd($transactions);
    $route2transactions = SmsDesktopCampaignId::with(['campaignData' => function ($query) {
                    $query->select('id', 'campaign_id', 'sd_message');
                }])
                ->when($title !== '', function ($query) use ($title) {
                    $query->where('sdci_campaign_title', 'like', "%{$title}%");
                })
                ->where('sdci_targeted_time', '>=', $q_start_date)
                ->where('sdci_targeted_time', '<', $q_end_date)
                ->where('user_id',Auth::id())
                ->orderBy('sdci_targeted_time', 'desc')
                ->get()
                ->groupBy(function($date) {
                    return Carbon::parse($date->sdci_targeted_time)->format('d M Y'); 
                });
    // dd($route2transactions);
    
        return view('user.campaign-report.campaign-report',compact('start_date','end_date','transactions','route2transactions'));
    }

    public function campaignReportDownloadPdf(Request $request){
        $title = trim((string) $request->camp_title);
        $q_start_date = Carbon::today();
        $q_end_date = Carbon::tomorrow();
        // $emp_id = Auth::guard('employee')->id();
        // $emp_create = EmployeeUser::where('id',$emp_id)->first();
        
        // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();

        $transactions = SmsCampaignId::with(['creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            }])
            ->when($title !== '', function ($query) use ($title) {
                $query->where('sci_campaign_title', 'like', "%{$title}%");
            })
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<', $q_end_date)
            ->where('user_id',Auth::id())
            ->orderBy('sci_targeted_time', 'desc')
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->sci_targeted_time)->format('d M Y'); 
            });
            // dd($transactions);
            // return view('user.campaign-report.campaign-report-download', compact('transactions'));
        
            $pdf = PDF::loadView('user.campaign-report.campaign-report-download', compact('transactions'));
            return $pdf->download('sms-bill-report.pdf');
    }

    public function CampaignreportDesktopDownloadPdf(Request $request){
        $total = 0;
        $day = '';
        $title = trim((string) $request->camp_title);
        $q_start_date = Carbon::today();
        $q_end_date = Carbon::tomorrow();
        // $emp_id = Auth::guard('employee')->id();
        // $emp_create = EmployeeUser::where('id',$emp_id)->first();
        
        // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();

        $route2transactions = SmsDesktopCampaignId::with(['campaignData' => function ($query) {
                        $query->select('id', 'campaign_id', 'sd_message');
                    }])
                    ->when($title !== '', function ($query) use ($title) {
                        $query->where('sdci_campaign_title', 'like', "%{$title}%");
                    })
                    ->where('sdci_targeted_time', '>=', $q_start_date)
                    ->where('sdci_targeted_time', '<', $q_end_date)
                    ->where('user_id',Auth::id())
                    ->orderBy('sdci_targeted_time', 'desc')
                    ->get()
                    ->groupBy(function($date) {
                        return Carbon::parse($date->sdci_targeted_time)->format('d M Y'); 
                    });

      
        // $groupCount = $route2transactions->map(function ($item, $key) {
        //     return $item + $item;
        // });
       
            
            // return view('user.campaign-report.campaign-desktop-report-download', compact('route2transactions'));
        
            $pdf = PDF::loadView('user.campaign-report.campaign-desktop-report-download', compact('route2transactions'));
            return $pdf->download('sms-bill-report.pdf');
    }
}
