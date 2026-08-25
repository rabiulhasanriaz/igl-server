<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\AccSmsBalance;
use App\Model\AccSmsRate;
use App\Model\Operator;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\SenderIdUserDefault;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCampaignId;
use App\Model\User;
use App\Model\UserDetail;
use Illuminate\Support\Facades\Cache;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Validator;

class ResellerController extends Controller
{
    //
    /*reseller list*/
    public function index()
    {
        $users = User::with('userDetail')->where('create_by', Auth::id())->get();
        return view('admin.reseller.reseller_list', compact('users'));

    }


    /*show create reseller form*/
    public function create()
    {
        return view('admin.reseller.reseller_registration');
    }


    /* create new reseller */
    public function store(Request $request)
    {
        /*validate reseller information*/
        $validateData = Validator::make($request->all(), [
            'company_name' => ['required'],
            'reseller_name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'unique:users,cellphone', new UserMobile],
            'password' => ['required', 'min:3'],
        ]);


        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        /*insert reseller information*/
        $createReseller = User::create([
            'create_by' => Auth::user()->id,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'cellphone' => $request->phone,
            'password' => bcrypt($request->password),
            'status' => '1',
            'role' => '4',
            'position' => (Auth::user()->position + 1),
        ]);

        if ($createReseller == true) {
            $randid = time();
            $api_key = '445' . $randid . $createReseller->id . $randid;
            /*insert reseller details information*/
            $resellerDetUpd = UserDetail::create([
                'user_id' => $createReseller->id,
                'domain_name' => Auth::user()->userDetail->domain_name,
                'name' => $request->reseller_name,
                'designation' => $request->designation,
                'address' => $request->address,
                'nid' => $request->nid,
                'dob' => $request->dob,
                'user_p' => $request->password,
                'api_key' => $api_key,
            ]);


            if ($resellerDetUpd == true) {
                /*if has reseller image then upload it adn save*/
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    $name = str_random(20) . $resellerDetUpd->id . '.' . $files->getClientOriginalExtension();
                    $destinationPath = 'assets/uploads/User_Logo';
                    $url = $destinationPath . "/" . $name;
                    $files->move($destinationPath, $name);
                    $logoUpd = UserDetail::where('id', $resellerDetUpd->id)->update([
                        'logo' => $name,
                    ]);
                }
                /*insert initial user sms rate as 0*/
                try {
                    $allOperators = Operator::orderBy('id', 'asc')->take(5)->get();
                    foreach (Auth::user()->smsRates as $allRate) {
                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createReseller->id,
                            'operator_id' => $allRate->operator_id,
                            'asr_masking' => $allRate->asr_masking,
                            'asr_nonmasking' => $allRate->asr_nonmasking,
                        ]);
                    }
                    $senderId = SenderIdRegister::where('sir_active', '1')->orderBy('id', 'desc')->first();
                    SenderIdUser::create([
                        'user_id' => $createReseller->id,
                        'sender_id' => $senderId->id,
                    ]);
                    SenderIdUserDefault::create([
                        'user_id' => $createReseller->id,
                        'sender_id' => $senderId->id,
                    ]);
                } catch (\Exception $e) {
                    session()->flash('message', 'Something went wrong to create user(error code: 030)');
                    session()->flash('type', 'danger');
                    return redirect()->back()->withInput();
                }

                /*send sms to created user*/
                $message = "Welcome To " . Auth::user()->userDetail->company_name . "\nYour SMS Portal Is Ready to Use\nURL: " . Auth::user()->userDetail->domain_name . "\nUID: " . $request->phone . "\nPass: " . $request->password;
                $message = rawurlencode($message);
                $number = '88'.$request->phone;

                $client = new Client();
                $url = config('app.url')."/api/v1/send?api_key=".Auth::user()->userDetail->api_key."&contacts=".$number."&senderid=8804445604445&msg=".$message."&for_registration=adminToReseller";

                $res = $client->request('GET', $url);
                $ret = $res->getBody();

                session()->flash('message', 'User Registration Successfully completed');
                session()->flash('type', 'success');
                return redirect()->back();

            } else {
                session()->flash('message', 'Something went wrong to create user(error code: 020)');
                session()->flash('type', 'danger');
                return redirect()->back()->withInput();
            }
        } else {
            session()->flash('message', 'Something went wrong to create user(error code: 010)');
            session()->flash('type', 'danger');
            return redirect()->back()->withInput();
        }
    }


    /*show reseller edit form*/
    public function edit($id)
    {
        try {
            $userInfo = User::with('userDetail')->where('id', $id)->first();
            if ($userInfo) {
                return view('admin.reseller.edit_reseller_account', compact('userInfo'));
            } else {
                session()->flash('message', 'can\'t find this user. please try again');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('message', 'Something went wrong to edit user');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*update reseller details*/
    public function update(Request $request, $id)
    {

        $updUser = User::where('id', $id)->first();
        if ($updUser) {
            /*validate reseller information*/
            $validateData = Validator::make($request->all(), [
                'company_name' => ['required'],
                'reseller_name' => ['required'],
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
                'phone' => ['required', new UserMobile, Rule::unique('users', 'cellphone')->ignore($id)],
                'password' => ['required', 'min:3'],
            ]);

            if ($validateData->fails()) {
                return redirect()->back()->withErrors($validateData);
            }

            try {
                $updUserDetails = UserDetail::where('user_id', $id)->first();
                $updUser->company_name = $request->company_name;
                
                $updUser->email = $request->email;
                $updUser->cellphone = $request->phone;
                $updUser->password = bcrypt($request->password);
                $updUserDetails->designation = $request->designation;
                $updUserDetails->address = $request->address;
                $updUserDetails->name = $request->reseller_name;
                $updUserDetails->user_p = $request->password;

                /*if has reseller image then upload it adn save*/
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    $name = str_random(20) . $updUser->id . '.' . $files->getClientOriginalExtension();
                    $destinationPath = 'assets/uploads/User_Logo';
                    $url = $destinationPath . "/" . $name;
                    $files->move($destinationPath, $name);
                    $updUserDetails->logo = $name;
                }
                $updUser->report_permission = $request->permission;

                $updUser->save();
                $updUserDetails->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Successfully updated reseller information');

                $users = User::with('userDetail')->get();
                return redirect()->route('admin.reseller.index', compact('users'));


            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'something went wrong to update user. please try again.....!');
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find this user. please try again.....!');
            return redirect()->back();
        }

    }


    public function treeView()
    {
        $roots = User::with('myUsers')->where('position', '0')->get();
        return view('admin.reseller.reseller_tree_view', compact('roots'));
    }


    /*suspend a reseller*/
    public function suspend($id)
    {
        try {

            $suspendUser = User::where('id', $id)->first();
            if ($suspendUser) {
                $suspendUser->status = '2';

                $suspendUser->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Suspended successfully');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this user. please try again........');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to suspend user. please try again........');
            return redirect()->back();
        }
    }


    /*active a reseller*/
    public function active($id)
    {
        try {

            $activeUser = User::where('id', $id)->first();
            if ($activeUser) {
                $activeUser->status = '1';

                $activeUser->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Re-Active successfully');
                return redirect()->back();
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this user. please try again........');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to re-active user. please try again........');
            return redirect()->back();
        }
    }


    /*go to this reseller account*/
    public function goToThisAccount($id)
    {
        $user = User::where(['id'=>$id])->first();
        if($user){

            try{
                if(Auth::attempt(['email'=>$user->email, 'password'=>$user->userDetail->user_p])){
                    if(Auth::user()->status=='1'){
                        return redirect('/home');
                    }
                    elseif(Auth::user()->status=='2'){
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was suspended');
                        return redirect()->back();
                    }
                    else{
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was expired');
                        return redirect()->back();
                    }
                }
                else{
                    session()->flash('type', 'danger');
                    session()->flash('message', 'login credential was wrong...');
                    return redirect()->back();
                }

            }catch (\Exception $e){
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong to go this user account. please try again1........'.$e->getMessage());
                return redirect()->back();
            }
        }else{
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to go this user account. please try again2........');
            return redirect()->back();
        }
    }
public function smsMonitoringadminDashboard(Request $request, $date = null)
{
    $selectedDate = $date ?? now()->toDateString();
    $date = Carbon::parse($selectedDate);

    $startOfMonth = $date->copy()->startOfMonth();
    $endOfMonth = $date->copy()->endOfMonth();

    $monthSms = SmsCampaign_24h::whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->selectRaw('DATE(created_at) as date, COUNT(*) as total_sms, COUNT(DISTINCT user_id) as active_users')
        ->groupBy('date')
        ->get()
        ->keyBy('date');

    $daysInMonth = [];
    $currentDay = $startOfMonth->copy();
    while ($currentDay <= $endOfMonth) {
        $dayKey = $currentDay->toDateString();
        $smsData = $monthSms[$dayKey] ?? null;

        $daysInMonth[] = [
            'date' => $dayKey,
            'day' => $currentDay->day,
            'is_current_month' => true,
            'is_today' => $currentDay->isToday(),
            'is_selected' => $dayKey === $selectedDate,
            'total_sms' => $smsData->total_sms ?? 0,
            'active_users' => $smsData->active_users ?? 0,
        ];

        $currentDay->addDay();
    }

    $selectedDayStart = $date->copy()->startOfDay();
    $selectedDayEnd = $date->copy()->endOfDay();

    // Get all SMS stats including masking/non-masking using sct_sms_type
    // sct_sms_type: 1 = NonMasking, 2 = Masking
    $selectedSmsData = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
        ->selectRaw('
            COUNT(*) as total_sms,
            COUNT(DISTINCT user_id) as active_users,
            SUM(CASE WHEN sct_sms_type = 2 THEN 1 ELSE 0 END) as masking_count,
            SUM(CASE WHEN sct_sms_type = 1 THEN 1 ELSE 0 END) as non_masking_count
        ')
        ->first();

    $activeUsers = $selectedSmsData->active_users ?? 0;
    $allUsers = User::where('status', 1)->where('role', 5)->count();
    $inactiveUsers = $allUsers - $activeUsers;
    $totalSms = $selectedSmsData->total_sms ?? 0;
    $maskingCount = $selectedSmsData->masking_count ?? 0;
    $nonMaskingCount = $selectedSmsData->non_masking_count ?? 0;

    $topUsers = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
        ->select('user_id', DB::raw('COUNT(*) as sms_count'))
        ->with('user')
        ->groupBy('user_id')
        ->orderByDesc('sms_count')
        ->get();

    return view('admin.reseller.monitoring', [
        'daysInMonth'       => $daysInMonth,
        'selectedDate'      => $selectedDate,
        'currentMonth'      => $date->format('F Y'),
        'totalSms'          => $totalSms,
        'maskingCount'      => $maskingCount,
        'nonMaskingCount'   => $nonMaskingCount,
        'activeUsers'       => $activeUsers,
        'inactiveUsers'     => $inactiveUsers,
        'allUsers'          => $allUsers,
        'topUsers'          => $topUsers,
        'prevMonth'         => $date->copy()->subMonth()->toDateString(),
        'nextMonth'         => $date->copy()->addMonth()->toDateString(),
    ]);
}
public function getSmsChartData(Request $request)
{
    $date = now();

    $chartData = [];
    for ($i = 15; $i >= 0; $i--) {
        $chartDay = $date->copy()->subDays($i);
        $dayStart = $chartDay->copy()->startOfDay();
        $dayEnd = $chartDay->copy()->endOfDay();

        $daySms = SmsCampaign_24h::whereBetween('created_at', [$dayStart, $dayEnd])->count();

        $chartData[] = [
            'date' => $chartDay->format('M j'),
            'sms_count' => $daySms,
        ];
    }

    return response()->json($chartData);
}
// Add this method to your controller
public function getSmsUsersData(Request $request)
{
    $selectedDate = $request->get('date', now()->toDateString());
    $date = Carbon::parse($selectedDate);
    
    $selectedDayStart = $date->copy()->startOfDay();
    $selectedDayEnd = $date->copy()->endOfDay();

    $selectedSmsData = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
        ->selectRaw('
            COUNT(*) as total_sms, 
            COUNT(DISTINCT user_id) as active_users,
            SUM(CASE WHEN sct_sms_type = 2 THEN 1 ELSE 0 END) as masking_count,
            SUM(CASE WHEN sct_sms_type = 1 THEN 1 ELSE 0 END) as non_masking_count
        ')
        ->first();

    $activeUsers = $selectedSmsData->active_users ?? 0;
    $allUsers = User::where('status', 1)->where('role', 5)->count();
    $inactiveUsers = $allUsers - $activeUsers;
    $totalSms = $selectedSmsData->total_sms ?? 0;
    $maskingCount = $selectedSmsData->masking_count ?? 0;
    $nonMaskingCount = $selectedSmsData->non_masking_count ?? 0;

    $topUsers = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
        ->select('user_id', DB::raw('COUNT(*) as sms_count'))
        ->with('user')
        ->groupBy('user_id')
        ->orderByDesc('sms_count')
        ->get();
        
    // Calculate stats for response
    $stats = [
        'totalSms' => $totalSms,
        'maskingCount' => $maskingCount,
        'nonMaskingCount' => $nonMaskingCount,
        'activeUsers' => $activeUsers,
        'inactiveUsers' => $inactiveUsers,
        'allUsers' => $allUsers,
        'activityRate' => $allUsers > 0 ? number_format(($activeUsers/$allUsers)*100, 1) : 0,
        'activeProgress' => $allUsers > 0 ? ($activeUsers/$allUsers)*100 : 0,
        'inactiveProgress' => $allUsers > 0 ? ($inactiveUsers/$allUsers)*100 : 0,
    ];
    
    // Render the table view
    $html = view('admin.reseller.top_users_table', [
        'topUsers' => $topUsers,
        'totalSms' => $totalSms
    ])->render();
    
    return response()->json([
        'html' => $html,
        'stats' => $stats
    ]);
}
    public function streamSmsUpdates()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        
        // Keep connection alive
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');

        $lastData = null;

        while (true) {
            $currentDate = now()->toDateString();
            
            // Get current data
            $selectedDayStart = now()->copy()->startOfDay();
            $selectedDayEnd = now()->copy()->endOfDay();

            $selectedSmsData = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
                ->selectRaw('
                    COUNT(*) as total_sms,
                    COUNT(DISTINCT user_id) as active_users,
                    SUM(CASE WHEN sct_sms_type = 2 THEN 1 ELSE 0 END) as masking_count,
                    SUM(CASE WHEN sct_sms_type = 1 THEN 1 ELSE 0 END) as non_masking_count
                ')
                ->first();

            $activeUsers = $selectedSmsData->active_users ?? 0;
            $allUsers = User::where('status', 1)->where('role', 5)->count();
            $inactiveUsers = $allUsers - $activeUsers;
            $totalSms = $selectedSmsData->total_sms ?? 0;
            $maskingCount = $selectedSmsData->masking_count ?? 0;
            $nonMaskingCount = $selectedSmsData->non_masking_count ?? 0;

            // Get top users
            $topUsers = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
                ->select('user_id', DB::raw('COUNT(*) as sms_count'))
                ->with('user')
                ->groupBy('user_id')
                ->orderByDesc('sms_count')
                ->get();

            // Prepare data
            $currentData = [
                'totalSms' => $totalSms,
                'maskingCount' => $maskingCount,
                'nonMaskingCount' => $nonMaskingCount,
                'activeUsers' => $activeUsers,
                'inactiveUsers' => $inactiveUsers,
                'allUsers' => $allUsers,
                'activityRate' => $allUsers > 0 ? number_format(($activeUsers/$allUsers)*100, 1) : 0,
                'activeProgress' => $allUsers > 0 ? ($activeUsers/$allUsers)*100 : 0,
                'inactiveProgress' => $allUsers > 0 ? ($inactiveUsers/$allUsers)*100 : 0,
                'topUsers' => $this->renderTopUsersHtml($topUsers, $totalSms),
                'timestamp' => now()->toDateTimeString(),
            ];

            // Send data only if changed
            if ($currentData !== $lastData) {
                echo "event: sms_update\n";
                echo "data: " . json_encode($currentData) . "\n\n";
                ob_flush();
                flush();
                $lastData = $currentData;
            }

            // Send heartbeat to keep connection alive
            echo "event: ping\n";
            echo "data: " . json_encode(['time' => now()->toDateTimeString()]) . "\n\n";
            ob_flush();
            flush();

            // Wait 1 second before next check
            sleep(1);
        }
    }

    /**
     * Render top users HTML for SSE response
     */
    private function renderTopUsersHtml($topUsers, $totalSms)
    {
        if ($topUsers->count() == 0) {
            return '<div class="alert alert-info"><i class="ace-icon fa fa-info-circle"></i> No SMS activity found for this date.</div>';
        }

        $html = '<div class="table-responsive">';
        $html .= '<table class="table table-striped table-bordered table-hover">';
        $html .= '<thead><tr>
            <th style="width:2%;">SL</th>
            <th style="width:15%;">User</th>
            <th style="width:8%;">Phone</th>
            <th style="width:5%;">Count</th>
            <th style="width:5%;">%</th>
            <th style="width:65%;">Ratios</th>
        </tr></thead><tbody>';

        foreach ($topUsers as $index => $user) {
            $percentage = $totalSms > 0 ? ($user->sms_count / $totalSms) * 100 : 0;

            // Progress bar color based on percentage
            if ($percentage >= 10) {
                $barColor = '#28a745';
            } elseif ($percentage >= 5) {
                $barColor = '#ffc107';
            } elseif ($percentage >= 2) {
                $barColor = '#fd7e14';
            } else {
                $barColor = '#d14747';
            }

            // Badge color based on SMS count
            if ($user->sms_count >= 10000) {
                $badgeColor = '#28a745';
            } elseif ($user->sms_count >= 5000) {
                $badgeColor = '#ffc107';
            } elseif ($user->sms_count >= 1000) {
                $badgeColor = '#fd7e14';
            } else {
                $badgeColor = '#d14747';
            }

            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . ($user->user->company_name ?? 'Unknown User') . '</td>
                <td>' . ($user->user->cellphone ?? '') . '</td>
                <td>
                    <span class="badge" style="background-color: ' . $badgeColor . '; color: #fff;">
                        ' . number_format($user->sms_count) . '
                    </span>
                </td>
                <td>
                    <span style="color: ' . ($percentage >= 10 ? '#28a745' : ($percentage >= 5 ? '#ffc107' : ($percentage >= 2 ? '#fd7e14' : '#d14747'))) . '; font-weight: bold;">
                        ' . number_format($percentage, 1) . '%
                    </span>
                </td>
                <td>
                    <div class="progress progress-mini" style="height:19px;">
                        <div class="progress-bar" 
                             style="width: ' . $percentage . '%; 
                                    line-height: 15px;
                                    background-color: ' . $barColor . ';
                                    color: #fff;
                                    font-weight: bold;">
                            ' . number_format($percentage, 1) . '%
                        </div>
                    </div>
                </td>
            </tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

}
