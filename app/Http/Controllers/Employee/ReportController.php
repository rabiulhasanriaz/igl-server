<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Model\AccSmsBalance;
use App\Model\EmployeeUser;
use App\Model\User;
use App\Model\SmsCampaignId;
use App\Model\SmsDesktopCampaignId;
use Auth;
use PDF;
use DB;

class ReportController extends Controller
{
    public function report(Request $request){
        
        
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }
        $emp_id = Auth::guard('employee')->id();
        $emp_create = EmployeeUser::where('id',$emp_id)->first();
        $users_list = User::where('employee_user_id',$emp_id)->where('status',1)->get();

        // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();
        // dd($users);
        
        $transactions = SmsCampaignId::where('user_id', $request->user)
            ->whereBetween('sci_targeted_time', [$q_start_date, $q_end_date])
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->sci_targeted_time)->format('d M Y'); 
            });
        $route2transactions = SmsDesktopCampaignId::where('user_id', $request->user)
                    ->whereBetween('sdci_targeted_time', [$q_start_date, $q_end_date])
                    ->where('sdci_targeted_time', '>=', $q_start_date)
                    ->where('sdci_targeted_time', '<=', $q_end_date)
                    ->get()
                    ->groupBy(function($date) {
                        return Carbon::parse($date->sdci_targeted_time)->format('d M Y'); 
                    });
        // dd($transactions);
        return view('employee.report',compact('transactions','start_date','end_date','route2transactions','users_list'));
    }

    public function reportDownloadPdf(Request $request){
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }
        $emp_id = Auth::guard('employee')->id();
        $emp_create = EmployeeUser::where('id',$emp_id)->first();
        
        // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();

        $transactions = SmsCampaignId::where('user_id', $request->user)
            ->whereBetween('sci_targeted_time', [$q_start_date, $q_end_date])
            ->where('sci_targeted_time', '>=', $q_start_date)
            ->where('sci_targeted_time', '<=', $q_end_date)
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->sci_targeted_time)->format('d M Y'); 
            });
            // dd($transactions);
            // return view('employee.report-download', compact('transactions'));
        
            $pdf = PDF::loadView('employee.report-download', compact('transactions'));
            return $pdf->download('sms-bill-route-1-report.pdf');
    }

    public function reportDesktopDownloadPdf(Request $request){
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }
        $emp_id = Auth::guard('employee')->id();
        $emp_create = EmployeeUser::where('id',$emp_id)->first();
        
        // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();

        $route2transactions = SmsDesktopCampaignId::where('user_id', $request->user)
                    ->whereBetween('sdci_targeted_time', [$q_start_date, $q_end_date])
                    ->where('sdci_targeted_time', '>=', $q_start_date)
                    ->where('sdci_targeted_time', '<=', $q_end_date)
                    ->get()
                    ->groupBy(function($date) {
                        return Carbon::parse($date->sdci_targeted_time)->format('d M Y'); 
                    });
            // dd($transactions);
            // return view('employee.report-r2-download', compact('route2transactions'));
        
            $pdf = PDF::loadView('employee.report-r2-download', compact('route2transactions'));
            return $pdf->download('sms-bill-report.pdf');
    }

    public function reportDesktopDownloadPdfWithout(Request $request){
        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $q_start_date = $request->start_date." 00:00:00";
            $q_end_date = $request->end_date." 23:59:59";
        } else{
            $start_date = Carbon::now()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $q_start_date = Carbon::now()->startOfDay();
            $q_end_date = Carbon::now()->endOfDay();
        }
        $emp_id = Auth::guard('employee')->id();
        $emp_create = EmployeeUser::where('id',$emp_id)->first();
        
        // $users = User::where('create_by',$emp_create->create_by)->where('employee_user_id',$emp_id)->pluck('id')->toArray();

        $route2transactions = SmsDesktopCampaignId::where('user_id', $request->user)
                    ->whereBetween('sdci_targeted_time', [$q_start_date, $q_end_date])
                    ->where('sdci_targeted_time', '>=', $q_start_date)
                    ->where('sdci_targeted_time', '<=', $q_end_date)
                    ->get()
                    ->groupBy(function($date) {
                        return Carbon::parse($date->sdci_targeted_time)->format('d M Y'); 
                    });
            // dd($transactions);
            // return view('employee.report-r2-download', compact('route2transactions'));
        
            $pdf = PDF::loadView('employee.report-without-r2', compact('route2transactions'));
            return $pdf->download('sms-bill-report.pdf');
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

        $emp_id = Auth::guard('employee')->id();
        $emp_create = EmployeeUser::where('id',$emp_id)->first();
        
        $users = User::where('create_by',$emp_create->create_by)->pluck('id')->toArray();

        $reports = SmsCampaign::with('operator')
                                ->whereIn('user_id',$users)
                                ->where('created_at','>=',$q_start_date)
                                ->where('created_at','<=',$q_end_date)
                                ->get();
        dd($reports);
        $fileName =  "report.xlsx";

        $serialiser = new ReportSerialiser();
        $excel = Exporter::make('Excel');
        $excel->load($reports);
        // dd($excel);
        $excel->setSerialiser($serialiser);

        return $excel->stream($fileName);
          
        // return view('user.reports.view-dlr.total-report-download', compact('reports'));
        // $pdf = PDF::loadView('user.reports.view-dlr.total-report-download', compact('reports'));
        // return $pdf->download('bill-report.pdf'); 
    }
}
