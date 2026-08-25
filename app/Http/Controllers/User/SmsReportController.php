<?php

namespace App\Http\Controllers\User;

use App\Model\SmsCampaign;
use App\Model\SmsCampaignId;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCamPending;
use App\Model\SmsDesktopCampaignId;
use App\Model\SmsDesktop;
use App\Model\SmsDesktop24h;
use App\Model\SmsDesktopPending;
use App\Model\User;
use App\Model\AccSmsBalance;
use App\Model\AccUserCreditHistory;
use App\Serialisers\ArchivedReportSerialiser;
use App\Serialisers\ApiReportsSerialiser;
use App\Serialisers\ArchivedDynamicReportSerialiser;
use App\Serialisers\TodaysReportSerialiser;
use App\Serialisers\TodaysDynamicReportSerialiser;
use App\Serialisers\ReportDownloadSerialiser;
use Carbon\Carbon;
use Exporter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use PDF;
class SmsReportController extends Controller
{
    public function pending_for_approval_sms_report()
    {
        $pending_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_status', 0)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.reports.pending_for_approval_sms_report', compact('pending_campaigns'));
    }

    public function rejected_sms_report()
    {
        $rejected_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_status', 2)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.reports.rejected_sms_report', compact('rejected_campaigns'));
    }

    /*start view dlr*/
public function todays_sms_report()
{
    $startDate = Carbon::today();
    $endDate = Carbon::tomorrow();

    $todays_campaigns = SmsCampaignId::with([
            'sender',
            'creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            }
        ])
        ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_campaign_status', 1)
        ->where(function ($query) {
            $query->where('sci_from_api', 0)
                  ->orWhereNull('sci_from_api');
        })
        ->where('sci_targeted_time', '>=', $startDate)
        ->where('sci_targeted_time', '<', $endDate)
        ->orderBy('id', 'desc')
        ->get();

    $todays_campaigns_by_api = SmsCampaignId::with([
            'sender',
            'creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            }
        ])
        ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_targeted_time', '>=', $startDate)
        ->where('sci_targeted_time', '<', $endDate)
        ->where('sci_from_api', 1)
        ->orderBy('id', 'desc')
        ->get();

    return view('user.reports.view-dlr.todays_sms_report', compact('todays_campaigns', 'todays_campaigns_by_api'));
}
    public function download_today_api_total_report(Request $request){
        
        // if ($request->has('start_date') && $request->has('end_date')) {
        //     $start_date = $request->start_date;
        //     $end_date = $request->end_date;
        //     $q_start_date = $request->start_date." 00:00:00";
        //     $q_end_date = $request->end_date." 23:59:59";
        // }else{
        //     $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
        //     $end_date = Carbon::now()->format('Y-m-d');
        //     $q_start_date = Carbon::now()->subDays(15);
        //     $q_end_date = Carbon::now();
        // }
        $start_date = Carbon::today();
        // dd($start_date);
        $end_date = Carbon::tomorrow();
        $api_reports = SmsCampaignId::with('sender')
                                ->where('user_id', Auth::id())
                                ->where('sci_targeted_time', '>=', $start_date)
                                ->where('sci_targeted_time', '<', $end_date)
                                ->where('sci_deal_type', '1')
                                ->where('sci_from_api',1)
                                ->orderBy('id', 'desc')
                                ->pluck('id')->toArray();
                                // dd($api_reports);
        $api = SmsCampaign_24h::where('user_id',Auth::id())
                ->select('sender_id', 'sct_cell_no', 'sct_message' ,'sct_sms_cost', 'created_at', 'sct_delivery_report')
                            ->where('sct_targeted_time','>=', $start_date)
                            ->where('sct_targeted_time','<', $end_date)
                            ->whereIn('campaign_id',$api_reports)
                            ->get();
        // dd($api);
        $fileName = "api_reports.xlsx";

        $serialiser = new TodaysReportSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($api);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);
    }

    public function downloadTodayReportCsv()
    {
        $todayStart = Carbon::today();
        $tomorrowStart = Carbon::tomorrow();

        $relations = [
            'sender',
            'creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            },
        ];

        $regularCampaigns = SmsCampaignId::with($relations)
            ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_targeted_time', '>=', $todayStart)
            ->where('sci_targeted_time', '<', $tomorrowStart)
            ->where(function ($query) {
                $query->where('sci_from_api', 0)->orWhereNull('sci_from_api');
            })
            ->orderBy('id', 'desc')
            ->get();

        $apiCampaigns = SmsCampaignId::with($relations)
            ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_targeted_time', '>=', $todayStart)
            ->where('sci_targeted_time', '<', $tomorrowStart)
            ->where('sci_from_api', 1)
            ->orderBy('id', 'desc')
            ->get();

        $campaignIds = $regularCampaigns->pluck('id')
            ->merge($apiCampaigns->pluck('id'))
            ->unique()
            ->values()
            ->all();

        $messages = collect();
        $recentMessages = collect();

        if (!empty($campaignIds)) {
            $messages = SmsCampaign::whereIn('campaign_id', $campaignIds)
                ->selectRaw('campaign_id, MAX(sc_message) as message_content')
                ->groupBy('campaign_id')
                ->pluck('message_content', 'campaign_id');

            $recentMessages = SmsCampaign_24h::whereIn('campaign_id', $campaignIds)
                ->selectRaw('campaign_id, MAX(sct_message) as message_content')
                ->groupBy('campaign_id')
                ->pluck('message_content', 'campaign_id');
        }

        foreach ($regularCampaigns->concat($apiCampaigns) as $campaign) {
            $campaign->message_content = $messages->get(
                $campaign->id,
                $recentMessages->get($campaign->id, 'No message found')
            );
        }

        $allCampaigns = $regularCampaigns->concat($apiCampaigns);
        $totalSmsCount = $allCampaigns->sum('report_sms_count');
        $totalSent = $allCampaigns->sum('report_recipient_count');
        $totalCost = $allCampaigns->sum('sci_total_cost');

        $fileName = 'today_sms_report_' . $todayStart->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($allCampaigns) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM makes Bangla display correctly when opened in Microsoft Excel.
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, [
                'SL', 'Campaign ID', 'Campaign Title', 'Type', 'Sender ID',
                'SMS Count', 'Total Sent', 'Total Cost', 'Submit Time', 'Message'
            ]);

            foreach ($allCampaigns->values() as $index => $campaign) {
                fputcsv($file, [
                    $index + 1,
                    $campaign->sci_campaign_id,
                    $campaign->sci_campaign_title,
                    (int) $campaign->sci_from_api === 1 ? 'API' : 'Regular',
                    optional($campaign->sender)->sir_sender_id ?: 'N/A',
                    $campaign->report_sms_count,
                    $campaign->report_recipient_count,
                    $campaign->sci_total_cost,
                    optional($campaign->sci_targeted_time)->format('Y-m-d H:i:s'),
                    str_replace(["\r", "\n"], ' ', $campaign->message_content),
                ]);
            }

            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function todays_dynamic_sms_report()
    {
        $todayStart = Carbon::today();
        $tomorrowStart = Carbon::tomorrow();
        $todays_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->whereIn('sdci_campaign_status', [1,0])
            ->where(function ($query) {
                $query->where('sdci_from_api', 0);
                $query->orWhere('sdci_from_api', NULL);
            })
            ->where('sdci_targeted_time', '>=', $todayStart)
            ->where('sdci_targeted_time', '<', $tomorrowStart)
            ->orderBy('id', 'desc')
            ->get();


        $todays_campaigns_by_api = SmsDesktopCampaignId::where('user_id', Auth::id())
                                ->where('sdci_deal_type', '1')
                                ->where('sdci_targeted_time', '>=', $todayStart)
                                ->where('sdci_targeted_time', '<', $tomorrowStart)
                                ->where('sdci_from_api',4)
                                ->groupBy('sdci_from_api')
                                ->selectRaw('*, sum(sdci_total_cost) as sum,sum(sdci_total_submitted) as sub_sum')
                                ->orderBy('id', 'desc')
                                ->first();
        return view('user.reports.dynamic.reports.view-dlr.todays_sms_report', compact('todays_campaigns','todays_campaigns_by_api'));
    }

public function show_todays_report_ajax(Request $request)
{
    $startDate = Carbon::today();
    $endDate = Carbon::tomorrow();

    $todays_reports = SmsCampaignId::with([
            'sender',
            'creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            }
        ])
        ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_targeted_time', '>=', $startDate)
        ->where('sci_targeted_time', '<', $endDate)
        ->where('sci_from_api', 1)
        ->orderBy('id', 'desc')
        ->get();

    return view('user.ajax.views.show_report_of_today', compact('todays_reports'));
}
    public function show_todays_dynamic_report_ajax(Request $request){
        $todayStart = Carbon::today();
        $tomorrowStart = Carbon::tomorrow();
        $todays_reports = SmsDesktopCampaignId::where('user_id', Auth::id())
                                ->where('sdci_deal_type', '1')
                                ->where('sdci_targeted_time', '>=', $todayStart)
                                ->where('sdci_targeted_time', '<', $tomorrowStart)
                                ->where('sdci_from_api',4)
                                ->orderBy('id', 'desc')
                                ->get();
        return view('user.ajax.dynamic.ajax.views.show_report_of_today', compact('todays_reports'));
    }

    /*download todays campaign details*/
    public function download_todays_report($campaign_id)
{
    $reports = SmsCampaign_24h::with('sender')
        ->select('sender_id', 'sct_cell_no', 'sct_message', 'sct_sms_cost', 'created_at', 'sct_delivery_report')
        ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
        ->orderBy('id', 'desc')
        ->get();

    $campaign = SmsCampaignId::where('id', $campaign_id)->first();
    $fileName = $campaign->sci_campaign_id . ".csv";

    $serialiser = new TodaysReportSerialiser();
    $excel = Exporter::make('Csv'); // Changed from 'Excel' to 'Csv'
    $excel->load($reports);
    $excel->setSerialiser($serialiser);

    return $excel->stream($fileName);
}

    public function download_dynamic_todays_report($campaign_id)
    {
        $reports = SmsDesktop24h::select('sdt_cell_no', 'sdt_message' ,'sdt_message' , 'sdt_sms_cost', 'created_at', 'sdt_delivery_report')
            ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
            ->orderBy('id', 'desc')
            ->get();
        // dd($reports);
        $campaign = SmsDesktopCampaignId::where('id', $campaign_id)->first();
        $fileName = $campaign->sdci_campaign_id . ".xlsx";

        $serialiser = new TodaysDynamicReportSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);

    }

public function archived_sms_report(Request $request)
{
    $start_date = trim((string) $request->start_date);
    $end_date = trim((string) $request->end_date);
    $archived_campaigns = collect();
    $archived_campaigns_by_api = collect();
    $cost = collect();
    $reportError = null;

    // Do not run expensive archive queries until the user searches by date.
    if (!$request->filled('start_date') || !$request->filled('end_date')) {
        return view('user.reports.view-dlr.archived_report', compact(
            'archived_campaigns',
            'archived_campaigns_by_api',
            'start_date',
            'end_date',
            'cost',
            'reportError'
        ));
    }

    try {
        $start = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $end_date)->startOfDay();
    } catch (\Exception $e) {
        $reportError = 'Please enter valid dates in YYYY-MM-DD format.';
        return view('user.reports.view-dlr.archived_report', compact(
            'archived_campaigns', 'archived_campaigns_by_api', 'start_date', 'end_date', 'cost', 'reportError'
        ));
    }

    if ($start->gt($end)) {
        $reportError = 'Start date must be before or equal to end date.';
        return view('user.reports.view-dlr.archived_report', compact(
            'archived_campaigns', 'archived_campaigns_by_api', 'start_date', 'end_date', 'cost', 'reportError'
        ));
    }

    $q_start_date = $start;
    $q_end_date = $end->copy()->addDay();

    $archived_campaigns = SmsCampaignId::with('sender')
        ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_targeted_time', '>=', $q_start_date)
        ->where('sci_targeted_time', '<', $q_end_date)
        ->where(function ($query) {
            $query->where('sci_from_api', 0)
                ->orWhereNull('sci_from_api');
        })
        ->orderBy('id', 'desc')
        ->get();

    $archived_campaign_ids = $archived_campaigns->pluck('id')->toArray();

    if (!empty($archived_campaign_ids)) {
        $cost = SmsCampaign::whereIn('campaign_id', $archived_campaign_ids)
            ->selectRaw('campaign_id, MAX(sc_sms_cost) as sc_sms_cost')
            ->groupBy('campaign_id')
            ->pluck('sc_sms_cost', 'campaign_id');
    }

    $archived_campaigns_by_api = SmsCampaignId::with([
            'sender',
            'creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            }
        ])
        ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_targeted_time', '>=', $q_start_date)
        ->where('sci_targeted_time', '<', $q_end_date)
        ->where('sci_from_api', 1)
        ->orderBy('id', 'desc')
        ->get();

    return view('user.reports.view-dlr.archived_report', compact(
        'archived_campaigns',
        'archived_campaigns_by_api',
        'start_date',
        'end_date',
        'cost',
        'reportError'
    ));
}
    public function dynamic_archived_sms_report(Request $request)
    {
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
            $archived_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time', '>=', $q_start_date)
            ->where('sdci_targeted_time', '<=', $q_end_date)
            ->where(function ($query) {
                $query->where('sdci_from_api',0);
                $query->orWhere('sdci_from_api',NULL);
            })
            ->orderBy('id', 'desc')
            ->get();

            $archived_campaign = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time', '>=', $q_start_date)
            ->where('sdci_targeted_time', '<=', $q_end_date)
            ->where(function ($query) {
                $query->where('sdci_from_api',0);
                $query->orWhere('sdci_from_api',NULL);
            })
            ->orderBy('id', 'desc')
            ->pluck('id')->toArray();
            $cost = SmsDesktop::whereIn('campaign_id',$archived_campaign)
                                ->groupBy('campaign_id')
                                ->get();
            // dd($cost);

            $archived_campaigns_by_api = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time', '>=', $q_start_date)
            ->where('sdci_targeted_time', '<=', $q_end_date)
            ->where('sdci_from_api',4)
            ->groupBy('sdci_from_api')
            ->selectRaw('*, sum(sdci_total_cost) as sum,sum(sdci_total_submitted) as sub_sum')
            ->orderBy('id', 'desc')
            ->first();
            // $total = $archived_campaigns_by_api->groupBy('sender_id')
            //         ->selectRaw('*, sum(sci_total_cost) as sum')
            //         ->pluck('sum','sender_id');
            // dd($archived_campaigns_by_api);


        return view('user.reports.dynamic.reports.view-dlr.archived_report', compact('archived_campaigns','archived_campaigns_by_api', 'start_date', 'end_date','cost'));
    }

public function show_api_report_ajax(Request $request)
{
    $start_date = $request->start_date . " 00:00:00";
    $end_date = $request->end_date . " 23:59:59";

    $api_reports = SmsCampaignId::with('sender')
        ->leftJoin(
            'acc_user_credit_histories',
            'acc_user_credit_histories.campaign_id',
            '=',
            'sms_campaign_ids.id'
        )
        ->where('sms_campaign_ids.user_id', Auth::id())
        ->where('sms_campaign_ids.sci_targeted_time', '>=', $start_date)
        ->where('sms_campaign_ids.sci_targeted_time', '<=', $end_date)
        ->where('sms_campaign_ids.sci_deal_type', '1')
        ->where('sms_campaign_ids.sci_from_api', 1)
        ->selectRaw('
            sms_campaign_ids.*,
            COALESCE(acc_user_credit_histories.uch_sms_count, 0) as sms_count
        ')
        ->orderBy('sms_campaign_ids.id', 'desc')
        ->get();

    return view('user.ajax.views.show_report_of_api', compact('api_reports'));
}
    public function show_dynamic_api_report_ajax(Request $request){
        $start_date = $request->start_date." 00:00:00";
        $end_date = $request->end_date." 23:59:59";
        $api_reports = SmsDesktopCampaignId::where('user_id', Auth::id())
                                ->where('sdci_targeted_time', '>=', $start_date)
                                ->where('sdci_targeted_time', '<=', $end_date)
                                ->where('sdci_deal_type', '1')
                                ->where('sdci_from_api',4)
                                ->orderBy('id', 'desc')
                                ->get();
        return view('user.ajax.dynamic.ajax.views.show_report_of_api',compact('api_reports'));
    }
    /*end view dlr*/

    public function download_api_total_report(Request $request){
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
        $start_date = $request->start_date." 00:00:00";
        // dd($start_date);
        $end_date = $request->end_date." 23:59:59";
        $api_reports = SmsCampaignId::with('sender')
                                ->where('user_id', Auth::id())
                                ->where('sci_targeted_time', '>=', $start_date)
                                ->where('sci_targeted_time', '<=', $end_date)
                                ->where('sci_deal_type', '1')
                                ->where('sci_from_api',1)
                                ->orderBy('id', 'desc')
                                ->pluck('id')->toArray();
                                // dd($api_reports);
        $api = SmsCampaign::where('user_id',Auth::id())
                ->select('sender_id', 'sc_cell_no', 'sc_message' ,'sc_sms_cost', 'created_at', 'sc_delivery_report')
                            ->where('sc_targeted_time','>=', $start_date)
                            ->where('sc_targeted_time','<=', $end_date)
                            ->whereIn('campaign_id',$api_reports)
                            ->get();
        // dd($api);
        $fileName = "api_reports.xlsx";

        $serialiser = new ApiReportsSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($api);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);
    }


public function download_archived_report($campaign_id)
{
    // Fetch campaign details
    $campaign = SmsCampaignId::where('id', $campaign_id)->first();
    
    if (!$campaign) {
        return redirect()->back()->with('error', 'Campaign not found');
    }
    
    $fileName = $campaign->sci_campaign_id . ".csv"; // Changed to .csv for guaranteed functionality

    // Current SMS records
    $reports = SmsCampaign::with('sender')
        ->select('sender_id', 'sc_cell_no', 'sc_message', 'sc_sms_cost', 'created_at', 'sc_delivery_report')
        ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
        ->orderBy('id', 'desc')
        ->get();

    // Archived SMS records
    $archivedReports = SmsCampaign_24h::with('sender')
        ->select(
            'sender_id', 
            'sct_cell_no as sc_cell_no', 
            'sct_message as sc_message', 
            'sct_sms_cost as sc_sms_cost', 
            'created_at', 
            'sct_delivery_report as sc_delivery_report'
        )
        ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
        ->orderBy('id', 'desc')
        ->get();

    // Merge both into a single collection
    $allReports = $reports->concat($archivedReports);

    // Check if any reports exist
    if ($allReports->isEmpty()) {
        return redirect()->back()->with('warning', 'No records found for this campaign');
    }

    // Mask middle 2 digits of phone numbers
    $allReports->transform(function ($item) {
        if (!empty($item->sc_cell_no) && strlen($item->sc_cell_no) >= 7) {
            $item->sc_cell_no = substr($item->sc_cell_no, 0, 5) . '**' . substr($item->sc_cell_no, 7);
        }
        return $item;
    });

    // Set headers for CSV download
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        'Expires' => '0'
    ];

    $callback = function() use ($allReports) {
        $file = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel to handle Unicode properly
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        fputcsv($file, [
            'Sender ID', 
            'Mobile Number', 
            'Message', 
            'SMS Cost (BDT)', 
            'Submit Time', 
            'Delivery Status'
        ]);
        
        foreach ($allReports as $report) {
            // Get sender ID safely
            $senderId = 'N/A';
            if (isset($report->sender) && isset($report->sender->sir_sender_id)) {
                $senderId = $report->sender->sir_sender_id;
            } elseif (isset($report->sender_id)) {
                $senderId = $report->sender_id;
            }
            
            // Clean message (remove line breaks for CSV)
            $message = $report->sc_message ?? 'N/A';
            $message = str_replace(["\r", "\n"], ' ', $message);
            
            // Format date
            $date = 'N/A';
            if (isset($report->created_at)) {
                $date = $report->created_at->format('Y-m-d H:i:s');
            }
            
            // Delivery status
            $status = !empty($report->sc_delivery_report) ? 'Delivered' : 'Pending';
            
            fputcsv($file, [
                $senderId,
                $report->sc_cell_no ?? 'N/A',
                $message,
                $report->sc_sms_cost ?? '0.00',
                $date,
                $status
            ]);
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
    public function download_api_report(Request $request){
        $start_date = $request->start_date." 00:00:00";
        $end_date = $request->end_date." 23:59:59";
        $api_reports = SmsCampaignId::with('sender')
                                ->where('user_id', Auth::id())
                                ->where('sci_targeted_time', '>=', $start_date)
                                ->where('sci_targeted_time', '<=', $end_date)
                                ->where('sci_deal_type', '1')
                                ->where('sci_from_api',1)
                                ->orderBy('id', 'desc')
                                ->pluck('id')->toArray();
        $api = SmsCampaign::where('user_id',Auth::id())
                ->select('sender_id', 'sc_cell_no', 'sc_message' ,'sc_sms_cost', 'created_at', 'sc_delivery_report')
                            ->whereIn('campaign_id',$api_reports)
                            ->get();
        //dd($api_reports);
        $fileName = "api_reports.xlsx";

        $serialiser = new ApiReportsSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($api_reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);
    }

    public function download_dynamic_archived_report($campaign_id)
    {
        $reports = SmsDesktop::select('sd_cell_no', 'sd_message' ,'sd_sms_cost', 'created_at', 'sd_delivery_report')
            ->where(['user_id' => Auth::id(), 'campaign_id' => $campaign_id])
            ->orderBy('id', 'desc')
            ->get();

        $campaign = SmsDesktopCampaignId::where('id', $campaign_id)->first();
        $fileName = $campaign->sdci_campaign_id . ".xlsx";

        $serialiser = new ArchivedDynamicReportSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);

    }

    public function reportDownload(Request $request){
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        }else{
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->subDays(15);
            $q_end_date = Carbon::now();
        }
        $reports = SmsCampaign::with('sender', 'operator')
                                ->where('user_id',Auth::id())
                                ->where('created_at','>=',$q_start_date)
                                ->where('created_at','<=',$q_end_date)
                                ->get();
        $fileName =  "report.xlsx";

        $serialiser = new ReportDownloadSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);
        // dd($reports);  
        // return view('user.reports.view-dlr.total-report-download', compact('reports'));
        // $pdf = PDF::loadView('user.reports.view-dlr.total-report-download', compact('reports'));
        // return $pdf->download('bill-report.pdf'); 
    }


    /*start campaign dlr*/
    public function todays_campaign_sms_report()
    {
        $todayStart = Carbon::today();
        $tomorrowStart = Carbon::tomorrow();
        $todays_campaigns = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '2')
            ->where('sci_targeted_time', '>=', $todayStart)
            ->where('sci_targeted_time', '<', $tomorrowStart)
            ->orderBy('id', 'desc')
            ->get();


        return view('user.reports.campaign-dlr.todays_campaign_sms_report', compact('todays_campaigns'));
    }

    public function todays_dynamic_campaign_sms_report()
    {
        // dd('a');
        $todayStart = Carbon::today();
        $tomorrowStart = Carbon::tomorrow();
        $todays_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
                                                ->where('sdci_deal_type', '2')
                                                ->where('sdci_targeted_time', '>=', $todayStart)
                                                ->where('sdci_targeted_time', '<', $tomorrowStart)
                                                ->orderBy('id', 'desc')
                                                ->get();

                                                // dd($todays_campaigns);
        return view('user.reports.dynamic.reports.campaign-dlr.todays_campaign_sms_report', compact('todays_campaigns'));
    }

   public function archived_campaign_report()
{
    $archived_campaigns = SmsCampaignId::with('sender')
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '2')
        ->where('sci_targeted_time', '<=', Carbon::now()->subHours(24))
        ->orderBy('id', 'desc')  // This will show latest first
        ->get();
    
    return view('user.reports.campaign-dlr.archived_campaign_report', compact('archived_campaigns'));
}
public function showArchivedReportDetail(Request $request)
{
    try {
        $campaign_id = $request->campaign_id;
        
        // Log the campaign ID for debugging
        \Log::info('showArchivedReportDetail called with campaign_id: ' . $campaign_id);
        \Log::info('User ID: ' . Auth::id());
        
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Get campaign info for title
        $campaign = SmsCampaignId::where('id', $campaign_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$campaign) {
            \Log::warning('Campaign not found: ' . $campaign_id . ' for user: ' . Auth::id());
            return response()->json(['error' => 'Campaign not found'], 404);
        }
        
        \Log::info('Campaign found: ' . $campaign->sci_campaign_id);
        \Log::info('Campaign total submitted: ' . $campaign->sci_total_submitted);
        
        // Get the archived campaign details (24h table)
        $reports = SmsCampaign_24h::with('sender')
            ->where('user_id', Auth::id())
            ->where('campaign_id', $campaign_id)
            ->orderBy('id', 'desc')
            ->get();
        
        \Log::info('Reports from 24h table: ' . $reports->count());
        
        // If no records in 24h table, try the main table
        if ($reports->isEmpty()) {
            $reports = SmsCampaign::with('sender')
                ->where('user_id', Auth::id())
                ->where('campaign_id', $campaign_id)
                ->orderBy('id', 'desc')
                ->get();
            
            \Log::info('Reports from main table: ' . $reports->count());
        }
        
        // Return view with reports and campaign
        return view('user.ajax.views.show_archived_report', compact('reports', 'campaign'));
        
    } catch (\Exception $e) {
        \Log::error('showArchivedReportDetail error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function archived_dynamic_campaign_report()
    {
        $archived_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
                                                    ->where('sdci_deal_type', '2')
                                                    ->where('sdci_targeted_time', '<=', Carbon::now()->subHours(24))
                                                    ->orderBy('id', 'desc')
                                                    ->get();
        return view('user.reports.dynamic.reports.campaign-dlr.archived_campaign_report', compact('archived_campaigns'));
    }
    /*end campaign dlr*/


    /*start campaign dlr*/

    public function pending_sms_report() {
        $schedule_pending = SmsCampaignId::where('user_id', Auth::id())
            ->where('sci_campaign_type', 2)->where('sci_targeted_time', '>', Carbon::now() )->get();
        return view('user.reports.schedule-sms.pending_sms', compact('schedule_pending'));
    }

    public function today_sms_report() {
        $start_time = Carbon::now()->format('Y-m-d'). " 00:00:00";
        // var_dump(date('Y-m-d H:i:s', strtotime($start_time)));
        // dd($start_time);
        $end_time = Carbon::now();

        $schedule_today_sent_sms = SmsCampaignId::where('user_id', Auth::id())
        ->where('sci_campaign_type', 2)->whereBetween('sci_targeted_time', [$start_time, $end_time])->get();
        return view('user.reports.schedule-sms.today_pending_sms', compact('schedule_today_sent_sms'));
    }

    public function schedule_archieved_sms_report()
    {
        $schedule_campaign_sms = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '2')
            ->where('sci_campaign_type', '2')
            ->orderBy('id', 'desc')
            ->get();
        return view('user.reports.schedule-sms.archieved_pending_sms', compact('schedule_campaign_sms'));
    }

    public function schedule_general_sms_report()
    {
        $schedule_general_sms = SmsCampaignId::with('sender')
            ->where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_campaign_type', '2')
            ->orderBy('id', 'desc')
            ->get();

        return view('user.reports.schedule-sms.general_sms_send', compact('schedule_general_sms'));
    }

    public function change_shedule_sms_time()
    {
        $campaign_id = request()->campaign_id;
        $campaign_id_for_pending_table = SmsCampaignId::where('sci_campaign_id', '=', $campaign_id)->value('id');

        $target_time = request()->new_date_time;
        $target_time = date('Y-m-d H:i:s', strtotime($target_time));

        DB::beginTransaction();
        try{
            SmsCampaignId::where('sci_campaign_id', '=', $campaign_id )->update([
                'sci_targeted_time' => $target_time
            ]);
            SmsCamPending::where('campaign_id', '=', $campaign_id_for_pending_table)->update([
                'scp_target_time' => $target_time
            ]);

        }catch(\Exception $e){
            DB::rollBack();
            session()->flash('message', 'Something went wrong');
            session()->flash('type', 'danger');
            return back();
        }
        DB::commit();
        session()->flash('message', 'Target Time Changed succesfully');
        session()->flash('type', 'success');
        return back();
    }
    /*end campaign dlr*/


    public function dynamic_pending_sms_report()
    {
        $pending_campaigns = SmsDesktopCampaignId::where('user_id', Auth::id())
            ->where('sdci_deal_type', '1')
            ->where('sdci_targeted_time','>=', Carbon::now())
            ->orderBy('id', 'desc')
            ->pluck('id')->toArray();
        $campaigns = SmsDesktopPending::with('campaignId')->where('sdp_target_time','>=', Carbon::now())
                                      ->whereIn('campaign_id',$pending_campaigns)
                                      ->groupBy('campaign_id')
                                      ->get();
            // dd($campaigns);

        return view('user.reports.dynamic.reports.pending_for_approval_sms_report', compact('campaigns'));
    }

    public function campaignScheduleUpdate(Request $request ,$id){
        // dd($id);
        $target_time = date('Y-m-d H:i:s', strtotime($request->target_time));
        DB::beginTransaction();
        try {
            $campaignId = SmsDesktopCampaignId::where('id',$id)->update([
                'sdci_targeted_time' => $target_time
            ]);
            $campPending = SmsDesktopPending::where('campaign_id',$id)->update([
                'sdp_target_time' => $target_time
            ]); 
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('type', 'danger');
            session()->flash('message', 'Something Went Wrong Update Schedule'.$e->getMessage());
            return redirect()->back();
        }
        DB::commit();
        session()->flash('type', 'success');
        session()->flash('message', 'successfully Updated Schedule');
        return redirect()->back();
    }

    public function rejectPendingSmsCampaigns($campaign_id)
    {
        // dd($campaign_id);
        DB::beginTransaction();
        try {
            $campaign_details = SmsDesktopCampaignId::where('id', $campaign_id)->first();
            // dd($campaign_details);
            if (!empty($campaign_details)) {
                SmsDesktopCampaignId::where('id', $campaign_id)
                    ->where('sdci_campaign_status', 0)
                    ->update([
                        'sdci_campaign_status' => 2
                    ]);
                    SmsDesktopPending::where('campaign_id', $campaign_id)
                    ->where('sdp_campaign_status', 0)
                    ->delete();


                /*credit user balance*/
                $user_id = $campaign_details->user_id;
                $user_det = User::where('id', $campaign_details->user_id)->first();
                $user_position = $user_det->position;

                while ($user_position >= 1) {

                    /*find cost details*/
                    $pre_acc_sms_balance = AccSmsBalance::where('asb_pay_to', $user_det->id)
                        ->where('asb_pay_ref', $campaign_details->sdci_campaign_id)
                        ->where('asb_pay_mode', '4')
                        ->where('asb_deal_type', '2')
                        ->orderBy('id', 'desc')
                        ->first();
                    // dd($pre_acc_sms_balance);
                    if (!empty($pre_acc_sms_balance)) {
                        /*refund cost*/
                        AccSmsBalance::create([
                            'asb_paid_by' => $user_det->create_by,
                            'asb_pay_to' => $user_det->id,
                            'asb_pay_ref' => $campaign_details->sdci_campaign_id,
                            'asb_credit' => $pre_acc_sms_balance->asb_debit,
                            'asb_debit' => 0,
                            'asb_submit_time' => Carbon::now(),
                            'asb_target_time' => Carbon::now(),
                            'asb_pay_mode' => '6', /*campaign refund*/
                            'asb_payment_status' => '1', /*1=paid, 2=checking*/
                            'asb_deal_type' => '1',/*1=deposit, 2=campaign*/
                            'credit_return_type' => '0',
                        ]);
                    }

                    $user_det = User::where('id', $user_det->create_by)->first();
                    $user_position = $user_det->position;
                }

                /*delete user credit history*/
                AccUserCreditHistory::where('campaign_id', $campaign_id)->where('user_id', $campaign_details->user_id)->delete();


            } else {
                DB::rollBack();

                session()->flash('type', 'danger');
                session()->flash('message', 'Invalid Campaign');
                return redirect()->back();
            }


        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to reject campaign'.$e->getMessage());
            return redirect()->back();
        }
        DB::commit();

        session()->flash('type', 'info');
        session()->flash('message', 'successfully rejected campaign');
        return redirect()->back();
    }
public function downloadArchivedReportCsv(Request $request)
{
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');

    if (!$start_date || !$end_date) {
        $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
    }

    try {
        $range_start = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay();
        $range_end = Carbon::createFromFormat('Y-m-d', $end_date)->startOfDay();
    } catch (\Exception $e) {
        abort(422, 'Invalid report date.');
    }

    if ($range_start->gt($range_end)) {
        abort(422, 'The start date must not be after the end date.');
    }

    // Load every campaign once. The half-open range can use the targeted-time index.
    $campaigns = SmsCampaignId::with([
            'sender',
            'creditHistory' => function ($query) {
                $query->select('id', 'campaign_id', 'uch_sms_count');
            },
        ])
        ->withCount(['pendingSmsData', 'sentSmsData', 'archivedSmsData'])
        ->where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_targeted_time', '>=', $range_start)
        ->where('sci_targeted_time', '<', $range_end->copy()->addDay())
        ->where(function ($query) {
            $query->whereIn('sci_from_api', [0, 1])
                ->orWhereNull('sci_from_api');
        })
        ->orderBy('id', 'desc')
        ->get();

    // Fetch one message per campaign in batches instead of querying inside a loop.
    $messages = collect();
    foreach ($campaigns->pluck('id')->chunk(1000) as $campaign_ids) {
        $current_messages = SmsCampaign::whereIn('campaign_id', $campaign_ids->all())
            ->groupBy('campaign_id')
            ->selectRaw('campaign_id, MAX(sc_message) as message_content')
            ->pluck('message_content', 'campaign_id');

        $archived_messages = SmsCampaign_24h::whereIn('campaign_id', $campaign_ids->all())
            ->groupBy('campaign_id')
            ->selectRaw('campaign_id, MAX(sct_message) as message_content')
            ->pluck('message_content', 'campaign_id');

        foreach ($campaign_ids as $campaign_id) {
            $messages->put(
                $campaign_id,
                $current_messages->get($campaign_id, $archived_messages->get($campaign_id, 'No message found'))
            );
        }
    }

    foreach ($campaigns as $campaign) {
        $campaign->message_content = $messages->get($campaign->id, 'No message found');
    }

    $regular_campaigns = $campaigns->filter(function ($campaign) {
        return (int) $campaign->sci_from_api !== 1;
    })->values();
    $api_campaigns = $campaigns->filter(function ($campaign) {
        return (int) $campaign->sci_from_api === 1;
    })->values();

    $all_campaigns = $regular_campaigns->concat($api_campaigns);
    $fileName = 'archived_sms_report_' . $start_date . '_to_' . $end_date . '.csv';

    return response()->stream(function () use ($all_campaigns) {
        $file = fopen('php://output', 'w');
        fwrite($file, "\xEF\xBB\xBF");
        fputcsv($file, [
            'SL', 'Campaign ID', 'Campaign Title', 'Type', 'Sender ID',
            'SMS Count', 'Total Sent', 'Total Cost', 'Submit Time', 'Message'
        ]);

        foreach ($all_campaigns->values() as $index => $campaign) {
            fputcsv($file, [
                $index + 1,
                $campaign->sci_campaign_id,
                $campaign->sci_campaign_title,
                (int) $campaign->sci_from_api === 1 ? 'API' : 'Regular',
                optional($campaign->sender)->sir_sender_id ?: 'N/A',
                $campaign->report_sms_count,
                $campaign->report_recipient_count,
                $campaign->sci_total_cost,
                optional($campaign->sci_targeted_time)->format('Y-m-d H:i:s'),
                str_replace(["\r", "\n"], ' ', $campaign->message_content),
            ]);
        }

        fclose($file);
    }, 200, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Cache-Control' => 'no-store, no-cache',
    ]);
}
    /**
     * Download API report as PDF by date range
     * Same pattern as downloadFlexiReport in LoadController
     */
    public function downloadApiReportPdf(Request $request)
    {
        // Get date range from request
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        if (!$start_date || !$end_date) {
            $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
        }
        
        $q_start_date = $start_date . ' 00:00:00';
        $q_end_date = $end_date . ' 23:59:59';
        
        // Get API campaign IDs
        $api_campaign_ids = SmsCampaignId::where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_from_api', 1)
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->pluck('id')
            ->toArray();
        
        // Get API reports
        $api_reports = SmsCampaign::with('sender')
            ->select('sender_id', 'sc_cell_no', 'sc_message', 'sc_sms_cost', 'created_at', 'sc_delivery_report')
            ->whereIn('campaign_id', $api_campaign_ids)
            ->orderBy('id', 'desc')
            ->get();
        
        // Get campaign summary
        $campaign_summary = SmsCampaignId::where('user_id', Auth::id())
            ->where('sci_deal_type', '1')
            ->where('sci_from_api', 1)
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->select('sci_campaign_id', 'sci_campaign_title', 'sci_targeted_time', 'sci_total_submitted', 'sci_total_cost')
            ->orderBy('id', 'desc')
            ->get();
        
        // Calculate totals
        $total_cost = $api_reports->sum('sc_sms_cost');
        $total_count = $api_reports->count();
        $delivered_count = $api_reports->where('sc_delivery_report', '!=', '')->count();
        
        $user = Auth::user();
        
        // Generate PDF - EXACTLY LIKE YOUR downloadFlexiReport METHOD
        $pdf = PDF::loadView('user.reports.pdf.api_report_pdf', [
            'reports' => $api_reports,
            'campaign_summary' => $campaign_summary,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_cost' => $total_cost,
            'total_count' => $total_count,
            'delivered_count' => $delivered_count,
            'user' => $user,
            'generated_at' => Carbon::now()
        ]);
        
        return $pdf->download('api_report_' . $start_date . '_to_' . $end_date . '.pdf');
    }
    public function downloadArchivedReportDetailsPdf(Request $request)
{
    // Get date range from request
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');
    
    // If no dates provided, use last 15 days
    if (!$start_date || !$end_date) {
        $start_date = Carbon::now()->subDays(15)->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');
    }
    
    $q_start_date = $start_date . ' 00:00:00';
    $q_end_date = $end_date . ' 23:59:59';
    
    // Get all campaign IDs for the date range (both regular and API)
    $campaign_ids = SmsCampaignId::where('user_id', Auth::id())
        ->where('sci_deal_type', '1')
        ->where('sci_targeted_time', '>=', $q_start_date)
        ->where('sci_targeted_time', '<=', $q_end_date)
        ->pluck('id')
        ->toArray();
    
    // Get all SMS details from both tables
    $regular_reports = SmsCampaign::with('sender')
        ->select('sender_id', 'sc_cell_no', 'sc_message', 'sc_sms_cost', 'created_at', 'sc_delivery_report')
        ->where('user_id', Auth::id())
        ->whereIn('campaign_id', $campaign_ids)
        ->orderBy('created_at', 'desc')
        ->get();
    
    $archived_reports = SmsCampaign_24h::with('sender')
        ->select('sender_id', 'sct_cell_no as sc_cell_no', 'sct_message as sc_message', 
                 'sct_sms_cost as sc_sms_cost', 'created_at', 'sct_delivery_report as sc_delivery_report')
        ->where('user_id', Auth::id())
        ->whereIn('campaign_id', $campaign_ids)
        ->orderBy('created_at', 'desc')
        ->get();
    
    // Merge both collections
    $all_reports = $regular_reports->concat($archived_reports);
    
    // Sort by created_at (newest first)
    $all_reports = $all_reports->sortByDesc('created_at');
    
    // Calculate totals
    $total_count = $all_reports->count();
    $total_cost = $all_reports->sum('sc_sms_cost');
    $delivered_count = $all_reports->where('sc_delivery_report', '!=', '')->count();
    $pending_count = $total_count - $delivered_count;
    
    // Get user info
    $user = Auth::user();
    
    // Generate PDF
    $pdf = PDF::loadView('user.reports.pdf.archived_report_details_pdf', [
        'reports' => $all_reports,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'total_count' => $total_count,
        'total_cost' => $total_cost,
        'delivered_count' => $delivered_count,
        'pending_count' => $pending_count,
        'user' => $user,
        'generated_at' => Carbon::now()
    ]);
    
    // Set paper size and orientation
    $pdf->setPaper('A4', 'landscape');
    
    return $pdf->download('archived_report_details_' . $start_date . '_to_' . $end_date . '.pdf');
}
}
