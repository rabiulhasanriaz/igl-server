<?php

namespace App\Http\Controllers\Reseller;


use App\Model\SenderIdUserDefault;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Model\User;
use App\Model\UserDetail;
use App\Model\AccUserCreditHistory;
use App\Rules\UserMobile;
use App\Model\SenderIdRegister;
use App\Model\SenderIdUser;
use App\Model\AccSmsBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Model\AccSmsRate;
use Illuminate\Validation\Rule;
use App\Model\SmsCampaign_24h;
use App\Model\SmsCampaignId;
class UserController extends Controller
{
    /*show all user list*/
    public function index()
    {
        $users = User::with('userDetail')->where('create_by', Auth::id())->whereNotIn('status', ['3'])->get();
        return view('reseller.users.user_list', compact('users'));
    }
	
	
	public function smsUsersNotLast10Days(Request $request)
    {
        $days = (int) $request->get('days', 10); // Number of days to check for activity
        $status = $request->get('status', 'inactive'); // 'active' or 'inactive'
        $export = $request->get('export', false);

        $dateThreshold = now()->subDays($days); // Users active after this date are "Active"
        $historyThreshold = now()->subDays(90); // Only consider last 90 days of SMS history

        // Subquery: last SMS date per user within last 90 days
        $lastSmsSub = AccUserCreditHistory::select('user_id', DB::raw('MAX(created_at) as last_sms_at'))
            ->where('created_at', '>=', $historyThreshold)
            ->groupBy('user_id');

        // Base user query
        $usersQuery = User::select('users.*', 'ls.last_sms_at')
            ->leftJoinSub($lastSmsSub, 'ls', function ($join) {
                $join->on('users.id', '=', 'ls.user_id');
            })
            ->where('users.status', 1)
            ->where('users.role', 5);

        // Filter by activity status
        if ($status === 'active') {
            $usersQuery->where('ls.last_sms_at', '>=', $dateThreshold);
        } else {
            $usersQuery->where(function ($q) use ($dateThreshold) {
                $q->whereNull('ls.last_sms_at')
                  ->orWhere('ls.last_sms_at', '<', $dateThreshold);
            });
        }

        $users = $usersQuery->get();

        // Safely transform each user for last SMS date and activity status
        $users->transform(function ($user) use ($dateThreshold) {
            // Convert last_sms_at to Carbon if not null
            $lastSms = $user->last_sms_at ? Carbon::parse($user->last_sms_at) : null;

            // Format last SMS date
            $user->last_sms_date = $lastSms ? $lastSms->format('Y-m-d H:i') : 'Never';

            // Determine activity status
            $user->activity_status = ($lastSms && $lastSms->gte($dateThreshold)) ? 'Active' : 'Inactive';

            return $user;
        });

        // Handle export if requested (CSV/Excel)
        if ($export) {
            // Example: return CSV/Excel download
            // return Excel::download(new UsersExport($users), 'users.csv');
        }

        // Return JSON for AJAX requests or standard view
        if ($request->ajax()) {
            return response()->json(['data' => $users]);
        }

        return view('reseller.users.inactive_user', compact('users'));
    }
    /* Get user balance for AJAX request */
/* Get user balance for AJAX request */
public function getUserBalance(Request $request)
{
    $userId = $request->get('user_id');
    
    if (!$userId) {
        return response()->json([
            'success' => false,
            'message' => 'User ID is required'
        ]);
    }
    
    // Get the user without any authorization check
    $user = User::find($userId);
    
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
    
    // Get user balance from AccSmsBalance table
    $balance = AccSmsBalance::where('asb_pay_to', $userId)
        ->selectRaw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) as balance')
        ->first();
    
    $balanceAmount = $balance ? floatval($balance->balance) : 0;
    
    return response()->json([
        'success' => true,
        'balance' => $balanceAmount,
        'user_id' => $userId,
        'company_name' => $user->company_name
    ]);
}
    /*show new user registration form*/
    public function create()
    {
        return view('reseller.users.user_registration');
    }


    /* create & store new user */
    public function store(Request $request)
    {

        /*validate user information*/
        $validateData = Validator::make($request->all(), [
            'company_name' => ['required'],
            'user_name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['required', 'unique:users,cellphone', new UserMobile],
            'password' => ['required', 'min:3'],
            'status' => ['required'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        if ($request->status == 'Reseller') {
            $role = '4';
            $permission = $request->permission;
        } else {
            $role = '5';
            $permission = implode(',',$request->permission);
        }
        // DB::beginTransaction();
        /*insert reseller information*/
        $userCreateData = [
            'create_by' => Auth::user()->id,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'cellphone' => $request->phone,
            'password' => bcrypt($request->password),
            'status' => '1',
            'role' => $role,
            'position' => (Auth::user()->position + 1),
            'permission' => $permission,
        ];
        // dd($userCreateData);
        $createUser = User::create($userCreateData);

        if ($createUser == true) {
            $randid = time();
            $api_key = '445' . $randid . $createUser->id . $randid;
            /*insert reseller details information*/
            $UserDetUpd = UserDetail::create([
                'user_id' => $createUser->id,
                'domain_name' => Auth::user()->userDetail->domain_name,
                'name' => $request->user_name,
                'designation' => $request->designation,
                'address' => $request->address,
                'nid' => $request->nid,
                'dob' => $request->dob,
                'user_p' => $request->password,
                'api_key' => $api_key,
            ]);


            if ($UserDetUpd == true) {
                /*if has reseller image then upload it adn save*/
                if ($request->hasFile('image')) {
                    $files = $request->file('image');
                    $name = str_random(20) . $UserDetUpd->id . '.' . $files->getClientOriginalExtension();
                    $destinationPath = 'assets/uploads/User_Logo';
                    $url = $destinationPath . "/" . $name;
                    $files->move($destinationPath, $name);
                    $logoUpd = UserDetail::where('id', $UserDetUpd->id)->update([
                        'logo' => $name,
                    ]);
                }
                /*insert initial user sms rate as 0*/
                try {
                    foreach (Auth::user()->smsRates as $allRate) {
                        AccSmsRate::create([
                            'country_id' => '1',
                            'user_id' => $createUser->id,
                            'operator_id' => $allRate->operator_id,
                            'asr_masking' => $allRate->asr_masking,
                            'asr_nonmasking' => $allRate->asr_nonmasking,
                        ]);
                    }
                    $senderId = SenderIdUser::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
                    SenderIdUser::create([
                        'user_id' => $createUser->id,
                        'sender_id' => $senderId->sender_id,
                    ]);
                    SenderIdUserDefault::create([
                        'user_id' => $createUser->id,
                        'sender_id' => $senderId->sender_id,
                    ]);
                } catch (\Exception $e) {
                    session()->flash('message', 'Something went wrong to create user(error code: 0030)' . $e->getMessage());
                    session()->flash('type', 'danger');
                    return redirect()->back()->withInput();
                }

                /*send sms to created user*/
                $message = "Welcome To " . Auth::user()->userDetail->company_name . "\nYour SMS Portal Is Ready to Use\nURL: " . Auth::user()->userDetail->domain_name . "\nUID: " . $request->phone . "\nPass: " . $request->password;
                $message = rawurlencode($message);
                $number = '88'.$request->phone;
                $defaultSenderId = SenderIdUserDefault::where('user_id', Auth::id())->first();
                $client = new Client();
                // config('app.url').
                $url = config('app.url')."/api/v1/send?api_key=".Auth::user()->userDetail->api_key."&contacts=".$number."&senderid=".$defaultSenderId->sender->sir_sender_id."&msg=".$message."&for_registration=resellerToUser";
                $res = $client->request('GET', $url);
                $ret = $res->getBody();

                session()->flash('message', 'User Registration Successfully completed');
                session()->flash('type', 'success');
                return redirect()->back();

            } else {
                session()->flash('message', 'Something went wrong to create user(error code: 0020)');
                session()->flash('type', 'danger');
                return redirect()->back()->withInput();
            }
        } else {
            session()->flash('message', 'Something went wrong to create user(error code: 0010)');
            session()->flash('type', 'danger');
            return redirect()->back()->withInput();
        }
    }


    /*show user edit form*/
    public function edit($id)
    {
        $user = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($user) {
            return view('reseller.users.edit_user_account', compact('user'));
        } else {
            session()->flash('message', 'Unknown user(error code: 0050)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*update user information*/
   public function update(Request $request, $id)
{
    $updUser = User::where('id', $id)->first();
    if (!$updUser) {
        session()->flash('type', 'danger');
        session()->flash('message', 'Can\'t find this user. Please try again.');
        return redirect()->back();
    }

    /* Validate user information */
    $validateData = Validator::make($request->all(), [
        'company_name' => ['required'],
        'user_name'    => ['required'],
        'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
        'phone'        => ['required', new UserMobile, Rule::unique('users', 'cellphone')->ignore($id)],
        'status'       => ['required'],
        'permission'   => ['nullable', 'array'], // allow null or array
        'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        'password'     => ['nullable', 'min:3'],
    ]);

    if ($validateData->fails()) {
        return redirect()->back()->withInput()->withErrors($validateData);
    }

    // Determine role
    $role = $request->status === 'Reseller' ? '4' : '5';

    try {
        // Update permission safely
        $updUser->permission = is_array($request->permission) ? implode(',', $request->permission) : '';

        // Update user info
        $updUser->company_name = $request->company_name;
        $updUser->email = $request->email;
        $updUser->cellphone = $request->phone;
        if (!empty($request->password)) {
            $updUser->password = bcrypt($request->password);
        }
        $updUser->role = $role;

        // Update user details
        $updUserDetails = UserDetail::where('user_id', $id)->first();
        if ($updUserDetails) {
            $updUserDetails->name = $request->user_name;
            $updUserDetails->designation = $request->designation ?? $updUserDetails->designation;
            $updUserDetails->address = $request->address ?? $updUserDetails->address;
            if (!empty($request->password)) {
                $updUserDetails->user_p = $request->password;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = \Str::random(20) . $updUser->id . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'assets/uploads/User_Logo';
                $file->move($destinationPath, $filename);
                $updUserDetails->logo = $filename;
            }
        }

        $updUser->save();
        $updUserDetails->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Successfully updated user information');

        $users = User::with('userDetail')
            ->where('create_by', Auth::id())
            ->whereNotIn('status', ['3'])
            ->get();

        return redirect()->route('reseller.user.index', compact('users'));
        
    } catch (\Exception $e) {
        session()->flash('type', 'danger');
        session()->flash('message', 'Something went wrong while updating user. Please try again! ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}


    /*show suspend user list*/
    public function suspendUser()
    {
        $users = User::with('userDetail')->where(['create_by' => Auth::id(), 'status' => 2])->get();
        return view('reseller.users.suspend_user_list', compact('users'));
    }


    /*suspend a user*/
    public function suspend($id)
    {
        $suspendUser = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($suspendUser) {
            try {
                $suspendUser->status = 2;
                $suspendUser->save();
                session()->flash('type', 'success');
                session()->flash('message', 'Suspend User Successfully completed');
                return redirect()->back();

            } catch (\Exception $e) {
                session()->flash('message', 'Something went wrong to suspend user(error code: 0040)');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Unknown user(error code: 0050)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*active a user*/
    public function active($id)
    {
        $activeUser = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($activeUser) {
            try {

                $activeUser->status = 1;
                $activeUser->save();
                session()->flash('type', 'success');
                session()->flash('message', 'Active User Successfully completed');
                return redirect()->back();

            } catch (\Exception $e) {
                session()->flash('message', 'Something went wrong to active user(error code: 0080)');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Unknown user(error code: 0090)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*delete a user*/
    public function delete($id)
    {
        $deleteUser = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($deleteUser) {
            try {
                $deleteUser->delete();
                session()->flash('type', 'success');
                session()->flash('message', 'User Deleted Successfully completed');
                return redirect()->back();

            } catch (\Exception $e) {
                session()->flash('message', 'Something went wrong to delete user(error code: 0060)');
                session()->flash('type', 'danger');
                return redirect()->back();
            }
        } else {
            session()->flash('message', 'Unknown user(error code: 0070)');
            session()->flash('type', 'danger');
            return redirect()->back();
        }
    }


    /*go to this reseller account*/
    public function goToThisAccount($id)
    {
        $user = User::where(['create_by' => Auth::id(), 'id' => $id])->first();
        if ($user) {
            try {
                if (Auth::attempt(['email' => $user->email, 'password' => $user->userDetail->user_p])) {
                    if (Auth::user()->status == '1') {
                        return redirect('/home');
                    } elseif (Auth::user()->status == '2') {
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was suspended');
                        return redirect()->back();
                    } else {
                        Auth::logout();
                        session()->flash('type', 'danger');
                        session()->flash('message', 'Your account was expired');
                        return redirect()->back();
                    }
                } else {
                    session()->flash('type', 'danger');
                    session()->flash('message', 'login credential was wrong...');
                    return redirect()->back();
                }

            } catch (\Exception $e) {
                session()->flash('type', 'danger');
                session()->flash('message', 'Something went wrong to go this user account. please try again1........' . $e->getMessage());
                return redirect()->back();
            }
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong to go this user account. please try again2........');
            return redirect()->back();
        }
    }
    
       public function smsUsersLastDayActivity()
{
    $oneDayAgo = now()->subDay();
    
    // Get users who have SMS activity in last 1 day
    $activeUserIds = AccUserCreditHistory::where('created_at', '>=', $oneDayAgo)
        ->pluck('user_id')
        ->unique();
    
    // Get all active users with role 5
    $allActiveUsers = User::where('status', 1)
        ->where('role', 5)
        ->get();
    
    // Get balances and last SMS dates for all users
    $userData = AccSmsBalance::whereIn('asb_pay_to', $allActiveUsers->pluck('id'))
        ->selectRaw('asb_pay_to, 
                   SUM(asb_credit) - SUM(asb_debit) as balance,
                   MAX(asb_submit_time) as last_sms_date')
        ->groupBy('asb_pay_to')
        ->get()
        ->keyBy('asb_pay_to');
    
    // Separate users into two groups
    $usersWithSms = $allActiveUsers->filter(function($user) use ($activeUserIds) {
        return $activeUserIds->contains($user->id);
    });
    
    $usersWithoutSms = $allActiveUsers->reject(function($user) use ($activeUserIds) {
        return $activeUserIds->contains($user->id);
    });
    
    // Add data to both groups
    $addUserData = function($user) use ($userData) {
        $data = $userData[$user->id] ?? null;
        $user->balance = $data ? $data->balance : 0;
        $user->last_sms_date = $data ? Carbon::parse($data->last_sms_date) : null;
        return $user;
    };
    
    $usersWithSms = $usersWithSms->map($addUserData);
    $usersWithoutSms = $usersWithoutSms->map($addUserData);
    
    return view('reseller.users.last_day_activity', [
        'usersWithSms' => $usersWithSms,
        'usersWithoutSms' => $usersWithoutSms,
        'withSmsCount' => $usersWithSms->count(),
        'withoutSmsCount' => $usersWithoutSms->count()
    ]);
}

   public function smsUsersLastDayActivitywithoutbalance()
{
    $yesterdayStart = now()->subDay()->startOfDay();
    $yesterdayEnd = now()->subDay()->endOfDay();

    
    // Get all active users with role 5
    $allActiveUsers = User::where('status', 1)
        ->where('role', 5)
        ->get();// Only select needed columns

    // Get user SMS data from SmsCampaign_24h table in a single query
    $userSmsData = SmsCampaign_24h::selectRaw('
            user_id, 
            COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as yesterday_sms_count,
            MAX(created_at) as last_sms_date
        ', [$yesterdayStart, $yesterdayEnd])
        ->whereIn('user_id', $allActiveUsers->pluck('id'))
        ->groupBy('user_id')
        ->get()
        ->keyBy('user_id');

    // Separate users and attach SMS info
    $usersWithSms = $allActiveUsers->filter(function ($user) use ($userSmsData) {
        return ($userSmsData[$user->id]->yesterday_sms_count ?? 0) > 0;
    })->map(function ($user) use ($userSmsData) {
        $user->sms_count = $userSmsData[$user->id]->yesterday_sms_count ?? 0;
        $user->last_sms_date = isset($userSmsData[$user->id]->last_sms_date) 
            ? Carbon::parse($userSmsData[$user->id]->last_sms_date) 
            : null;
        return $user;
    });

    $usersWithoutSms = $allActiveUsers->reject(function ($user) use ($userSmsData) {
        return ($userSmsData[$user->id]->yesterday_sms_count ?? 0) > 0;
    })->map(function ($user) use ($userSmsData) {
        $user->sms_count = 0;
        $user->last_sms_date = isset($userSmsData[$user->id]->last_sms_date) 
            ? Carbon::parse($userSmsData[$user->id]->last_sms_date) 
            : null;
        return $user;
    });

    // Calculate total SMS sent yesterday
    $totalSmsYesterday = $userSmsData->sum('yesterday_sms_count');

    return view('reseller.users.last_day_activity_without_balance', [
        'usersWithSms'      => $usersWithSms,
        'usersWithoutSms'   => $usersWithoutSms,
        'withSmsCount'      => $usersWithSms->count(),
        'withoutSmsCount'   => $usersWithoutSms->count(),
        'totalSmsYesterday' => $totalSmsYesterday,
        'yesterdayDate'     => $yesterdayStart->toDateString(),
        'yesterdayDayName'  => $yesterdayStart->format('l')
    ]);
}
    public function monthlySmsCountReseller()
    {
        // Get the start and end of the current month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Get daily SMS counts for the current month
        $dailyData = AccUserCreditHistory::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill in missing days with zero counts
        $completeDailyData = collect();
        $currentDate = $startOfMonth->copy();
        
        while ($currentDate <= $endOfMonth) {
            $dateString = $currentDate->toDateString();
            $dayData = $dailyData->firstWhere('date', $dateString);
            
            $completeDailyData->push([
                'date' => $currentDate->format('M j'),
                'count' => $dayData ? $dayData->count : 0
            ]);
            
            $currentDate->addDay();
        }

        $totalCount = $dailyData->sum('count');

        return response()->json([
            'success' => true,
            'daily_data' => $completeDailyData,
            'total_count' => $totalCount
        ]);
    }

    public function exportSmsActivity(Request $request)
    {
        // Get filter parameters
        $date = $request->get('date', now()->subDay()->toDateString());
        $type = $request->get('type', 'all'); // all, with_sms, without_sms

        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        // Get users based on filter
        if ($type === 'with_sms') {
            $activeUserIds = AccUserCreditHistory::whereBetween('created_at', [$startDate, $endDate])
                ->pluck('user_id')
                ->unique();

            $users = User::where('status', 1)
                ->where('role', 5)
                ->whereIn('id', $activeUserIds)
                ->get();
        } elseif ($type === 'without_sms') {
            $activeUserIds = AccUserCreditHistory::whereBetween('created_at', [$startDate, $endDate])
                ->pluck('user_id')
                ->unique();

            $users = User::where('status', 1)
                ->where('role', 5)
                ->whereNotIn('id', $activeUserIds)
                ->get();
        } else {
            $users = User::where('status', 1)
                ->where('role', 5)
                ->get();
        }

        // Generate CSV content
        $csvData = [];
        $csvData[] = ['Company', 'Mobile', 'Balance', 'Last SMS', 'Status'];

        foreach ($users as $user) {
            $status = $activeUserIds->contains($user->id) ? 'Active' : 'Inactive';
            $csvData[] = [
                $user->company_name,
                $user->cellphone,
                number_format($user->balance, 3),
                $user->last_sms_date ? $user->last_sms_date->format('Y-m-d H:i') : 'Never',
                $status
            ];
        }

        $filename = "sms_activity_{$date}_{$type}.csv";

        // Return CSV download
        return response()->streamDownload(function() use ($csvData) {
            $output = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, $filename);
    }

    public function sendReminder(Request $request)
    {
        $userId = $request->get('user_id');
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Here you would implement your reminder sending logic
        // This could be an email, SMS, or notification

        // For example:
        // Mail::to($user->email)->send(new SmsReminderMail());
        // or
        // sendSms($user->cellphone, 'Reminder: You haven\'t sent any SMS yesterday.');

        return response()->json([
            'success' => true,
            'message' => 'Reminder sent successfully'
        ]);
    }
   public function smsMonitoringDashboard(Request $request, $date = null)
{
    $selectedDate = $date ?? now()->toDateString();
    $date = Carbon::parse($selectedDate);

    // Start and end of month for calendar
    $startOfMonth = $date->copy()->startOfMonth();
    $endOfMonth = $date->copy()->endOfMonth();

    // --- Month SMS summary (SmsCampaign_24h) ---
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

    // --- Selected day stats ---
    $selectedDayStart = $date->copy()->startOfDay();
    $selectedDayEnd = $date->copy()->endOfDay();

    $selectedSmsData = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
        ->selectRaw('COUNT(*) as total_sms, COUNT(DISTINCT user_id) as active_users')
        ->first();

    $activeUsers = $selectedSmsData->active_users ?? 0;

    $allUsers = User::where('status', 1)->where('role', 5)->count();
    $inactiveUsers = $allUsers - $activeUsers;

    // --- Total SMS sent (selected day) ---
    $totalSms = $selectedSmsData->total_sms ?? 0;

    // --- Top 10 users by SMS volume (selected day) ---
    $topUsers = SmsCampaign_24h::whereBetween('created_at', [$selectedDayStart, $selectedDayEnd])
        ->select('user_id', DB::raw('COUNT(*) as sms_count'))
        ->with('user') // relationship to User
        ->groupBy('user_id')
        ->orderByDesc('sms_count')
        ->limit(10)
        ->get();

    // --- Chart data last 7 days (using SmsCampaign_24h) ---
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

    return view('reseller.users.monitoring', [
        'daysInMonth' => $daysInMonth,
        'selectedDate' => $selectedDate,
        'currentMonth' => $date->format('F Y'),
        'totalSms' => $totalSms,
        'activeUsers' => $activeUsers,
        'inactiveUsers' => $inactiveUsers,
        'allUsers' => $allUsers,
        'topUsers' => $topUsers,
        'chartData' => $chartData,
        'prevMonth' => $date->copy()->subMonth()->toDateString(),
        'nextMonth' => $date->copy()->addMonth()->toDateString(),
    ]);
}
/* Get low balance users data for DataTable (Server-side) - Only user_id = 3 sees all */
/* Get low balance users data for DataTable (Server-side) - WORKING */
/* Get low balance users data for DataTable (Server-side) - OPTIMIZED */
public function getLowBalanceUsersData(Request $request)
{
    $authUser = Auth::user();
    $threshold = $request->get('threshold', 2000);
    
    // DataTable parameters
    $draw = $request->get('draw');
    $start = $request->get('start', 0);
    $length = $request->get('length', 10);
    $search = $request->get('search')['value'] ?? '';
    
    // Ordering
    $orderColumn = $request->get('order')[0]['column'] ?? 0;
    $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
    
    $columns = ['id', 'company_name', 'email', 'cellphone', 'balance', 'status', 'created_at'];
    $orderBy = $columns[$orderColumn] ?? 'id';
    
    try {
        // First, get user IDs that have low balance (using a simpler query)
        $lowBalanceUserIds = AccSmsBalance::select('asb_pay_to')
            ->selectRaw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) as balance')
            ->groupBy('asb_pay_to')
            ->havingRaw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) > 0')
            ->havingRaw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) < ' . $threshold)
            ->pluck('asb_pay_to')
            ->toArray();
        
        if (empty($lowBalanceUserIds)) {
            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
        
        // Build user query with pre-filtered IDs
        $query = User::select(
                'users.id',
                'users.company_name',
                'users.email',
                'users.cellphone',
                'users.status',
                'users.created_at',
                'users.create_by'
            )
            ->whereIn('users.id', $lowBalanceUserIds)
            ->where('users.status', 1);
        
        // ONLY user_id = 3 can see ALL users
        if ($authUser->id != 3) {
            $query->where('users.create_by', $authUser->id);
        }
        
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('users.company_name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('users.cellphone', 'LIKE', "%{$search}%");
            });
        }
        
        // Get total count (with filters but before pagination)
        $recordsFiltered = $query->count();
        
        // Get total count without any filters (for recordsTotal)
        $totalUserIds = User::where('status', 1);
        if ($authUser->id != 3) {
            $totalUserIds->where('create_by', $authUser->id);
        }
        $recordsTotal = $totalUserIds->count();
        
        // Apply ordering and pagination
        $query->orderBy($orderBy, $orderDir);
        $users = $query->skip($start)->take($length)->get();
        
        // Now get balances only for these paginated users
        $userIds = $users->pluck('id')->toArray();
        
        $balances = AccSmsBalance::select('asb_pay_to')
            ->selectRaw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) as balance')
            ->whereIn('asb_pay_to', $userIds)
            ->groupBy('asb_pay_to')
            ->get()
            ->keyBy('asb_pay_to');
        
        // Prepare data for DataTable
        $data = [];
        foreach ($users as $user) {
            // Get balance (default to 0 if no balance record)
            $balanceObj = $balances->get($user->id);
            $balance = $balanceObj ? floatval($balanceObj->balance) : 0;
            
            // Get contact person
            $userDetail = UserDetail::where('user_id', $user->id)->first();
            $contactPerson = $userDetail ? $userDetail->name : 'N/A';
            
            // Get creator name
            $creator = User::find($user->create_by);
            $createdByName = $creator ? $creator->company_name : 'System';
            
            // Balance badge
            if ($balance < 500) {
                $balanceBadge = '<span class="label label-danger">⚠️ ' . number_format($balance, 3) . '</span>';
            } elseif ($balance < 1000) {
                $balanceBadge = '<span class="label label-warning">⚠️ ' . number_format($balance, 3) . '</span>';
            } else {
                $balanceBadge = '<span class="label label-info">' . number_format($balance, 3) . '</span>';
            }
            
            // Status badge
            $statusBadge = $user->status == 1 ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Suspended</span>';
            
            // Action buttons
            $actions = '
                <div class="btn-group btn-group-xs">
                    <a href="' . url('reseller/user/edit/' . $user->id) . '" class="btn btn-info" title="View/Edit">
                        <i class="ace-icon fa fa-eye"></i>
                    </a>
                    <button type="button" class="btn btn-warning send-reminder" 
                            data-id="' . $user->id . '" 
                            data-name="' . e($user->company_name) . '" 
                            data-balance="' . $balance . '" 
                            title="Send Reminder">
                        <i class="ace-icon fa fa-bell"></i>
                    </button>
                </div>
            ';
            
            $data[] = [
                'id' => $user->id,
                'company_name' => e($user->company_name),
                'contact_person' => e($contactPerson),
                'email' => e($user->email),
                'cellphone' => e($user->cellphone),
                'balance' => $balanceBadge,
                'status' => $statusBadge,
                'created_by_name' => e($createdByName),
                'created_at' => $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A',
                'actions' => $actions
            ];
        }
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error in getLowBalanceUsersData: ' . $e->getMessage());
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}
/* Get statistics for low balance users */
public function getLowBalanceStats(Request $request)
{
    $threshold = $request->get('threshold', 2000);
    $authUser = Auth::user();
    
    try {
        // Get balance sums
        $balanceSubquery = AccSmsBalance::select(
                'asb_pay_to',
                DB::raw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) as total_balance')
            )
            ->groupBy('asb_pay_to');
        
        // Main query
        $query = User::select(
                'users.id',
                DB::raw('COALESCE(balances.total_balance, 0) as balance')
            )
            ->leftJoinSub($balanceSubquery, 'balances', function($join) {
                $join->on('users.id', '=', 'balances.asb_pay_to');
            })
            ->where('users.status', 1);
        
        // ONLY user_id = 3 can see ALL users
        if ($authUser->id != 3) {
            $query->where('users.create_by', $authUser->id);
        }
        
        $users = $query->get();
        
        // Filter by balance
        $filteredUsers = $users->filter(function($user) use ($threshold) {
            return $user->balance > 0 && $user->balance < $threshold;
        });
        
        return response()->json([
            'success' => true,
            'total_users' => $filteredUsers->count(),
            'total_balance' => number_format($filteredUsers->sum('balance'), 3),
            'average_balance' => $filteredUsers->count() > 0 ? number_format($filteredUsers->sum('balance') / $filteredUsers->count(), 3) : 0,
            'threshold' => $threshold
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

/* Show low balance users view */
public function lowBalanceUsers(Request $request)
{
    $threshold = $request->get('threshold', 2000);
    return view('reseller.users.low_balance_users', compact('threshold'));
}

/* Export low balance users to CSV */
public function exportLowBalanceUsers(Request $request)
{
    $threshold = $request->get('threshold', 2000);
    $authUser = Auth::user();
    
    try {
        // Get balance sums
        $balanceSubquery = AccSmsBalance::select(
                'asb_pay_to',
                DB::raw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) as total_balance')
            )
            ->groupBy('asb_pay_to');
        
        // Main query
        $query = User::select(
                'users.id',
                'users.company_name',
                'users.email',
                'users.cellphone',
                'users.status',
                'users.created_at',
                'users.create_by',
                DB::raw('COALESCE(balances.total_balance, 0) as balance')
            )
            ->leftJoinSub($balanceSubquery, 'balances', function($join) {
                $join->on('users.id', '=', 'balances.asb_pay_to');
            })
            ->where('users.status', 1)
            ->having('balance', '>', 0)
            ->having('balance', '<', $threshold);
        
        // ONLY user_id = 3 can see ALL users
        if ($authUser->id != 3) {
            $query->where('users.create_by', $authUser->id);
        }
        
        $users = $query->get();
        
        $filename = "low_balance_users_" . date('Y-m-d_His') . ".csv";
        
        return response()->streamDownload(function() use ($users) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, ['ID', 'Company Name', 'Contact Person', 'Email', 'Mobile', 'Balance', 'Status', 'Created By', 'Created Date']);
            
            foreach ($users as $user) {
                $userDetail = UserDetail::where('user_id', $user->id)->first();
                $creator = User::find($user->create_by);
                
                fputcsv($output, [
                    $user->id,
                    $user->company_name,
                    $userDetail ? $userDetail->name : 'N/A',
                    $user->email,
                    $user->cellphone,
                    number_format(floatval($user->balance), 3),
                    $user->status == 1 ? 'Active' : 'Inactive',
                    $creator ? $creator->company_name : 'System',
                    $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }
            fclose($output);
        }, $filename);
        
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to export data: ' . $e->getMessage());
        return redirect()->back();
    }
}
}
