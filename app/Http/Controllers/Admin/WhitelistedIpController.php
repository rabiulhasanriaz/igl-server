<?php

namespace App\Http\Controllers\Admin;

use App\Model\User;
use App\Model\UserDetail;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Model\SmsIpDailyLimit;
use Validator;
use Carbon\Carbon;
class WhitelistedIpController extends Controller
{
    /*list of all users with whitelisted IPs*/
    public function index()
    {
        $users = User::where('role', 5) // role 5 = normal users
            ->with('userDetail')
            ->get();
        
        return view('admin.whitelistedIp.whitelisted_ip_list', compact('users'));
    }

    /*show form to add/update whitelisted IP for a specific user*/
    public function create()
    {
        $users = User::where('role', 5)->get(); // Get all normal users
        return view('admin.whitelistedIp.add_whitelisted_ip', compact('users'));
    }

    /*store or update whitelisted IP*/
    public function store(Request $request)
    {
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'white_listed_ip' => ['nullable', 'string'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        try {
            $userDetail = UserDetail::where('user_id', $request->user_id)->first();
            
            if (!$userDetail) {
                session()->flash('type', 'danger');
                session()->flash('message', 'User details not found. Please try again.');
                return redirect()->back()->withInput();
            }

            $userDetail->white_listed_ip = $request->white_listed_ip;
            $userDetail->save();

            session()->flash('type', 'success');
            session()->flash('message', 'Whitelisted IP updated successfully for user.');
            return redirect()->route('admin.whitelistedIp.index');

        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong. Please try again. Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /*show edit form for specific user's whitelisted IP*/
    public function edit($id)
    {
        try {
            $user = User::where('id', $id)->where('role', 5)->first();
            if($user) {
                $users = User::where('role', 5)->get();
                return view('admin.whitelistedIp.edit_whitelisted_ip', compact('user', 'users'));
            }
            else{
                session()->flash('type', 'danger');
                session()->flash('message', 'User not found. Please try again.');
                return redirect()->route('admin.whitelistedIp.index');
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong. Please try again.');
            return redirect()->route('admin.whitelistedIp.index');
        }
    }

    /*update whitelisted IP for specific user*/
    public function update(Request $request, $id)
    {
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'white_listed_ip' => ['nullable', 'string'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        try {
            $userDetail = UserDetail::where('user_id', $request->user_id)->first();
            
            if ($userDetail) {
                $userDetail->white_listed_ip = $request->white_listed_ip;
                $userDetail->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Whitelisted IP updated successfully.');
                return redirect()->route('admin.whitelistedIp.index');
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'User details not found. Please try again.');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong. Please try again.');
            return redirect()->back();
        }
    }

    /*delete/clear whitelisted IP for a user*/
    public function delete($id)
    {
        try {
            $userDetail = UserDetail::where('user_id', $id)->first();
            
            if($userDetail) {
                $userDetail->white_listed_ip = null;
                $userDetail->save();
                
                session()->flash('type', 'success');
                session()->flash('message', 'Whitelisted IP cleared successfully.');
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'User not found.');
            }
            
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong. Please try again.');
            return redirect()->back();
        }
    }

    /*check whitelisted IP status for a user via AJAX*/
    public function checkIpStatus($id)
    {
        try {
            $userDetail = UserDetail::where('user_id', $id)->first();
            
            if($userDetail) {
                $ipList = $userDetail->white_listed_ip;
                $ipArray = !empty($ipList) ? explode(',', $ipList) : [];
                
                return response()->json([
                    'success' => true,
                    'has_whitelist' => !empty($ipList),
                    'white_listed_ip' => $ipList,
                    'ip_list' => $ipArray,
                    'ip_count' => count($ipArray)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
   
public function nonWhitelistedUsers()
{
    $today = Carbon::today()->toDateString();

    // ✅ Users who have SMS activity today
    $users = User::where('role', 5)
        ->whereHas('smsLimits', function ($q) use ($today) {
            $q->where('limit_date', $today);
        })
        ->with([
            'userDetail',
            'smsLimits' => function ($q) use ($today) {
                $q->where('limit_date', $today);
            }
        ])
        ->get();

    foreach ($users as $user) {

        // ✅ Today's SMS count (from eager loaded relation)
        $user->today_count = $user->smsLimits->sum('sms_count');

        // ✅ Remaining limit
        $user->remaining = max(0, 50 - $user->today_count);

        // ✅ Whitelist check
        $user->has_whitelist = (
            $user->userDetail &&
            !empty($user->userDetail->white_listed_ip)
        );

        // ✅ Status setup
        if ($user->has_whitelist) {
            $user->access_type = 'whitelisted';
            $user->access_label = 'Whitelisted IP';
            $user->access_class = 'label-success';
            $user->limit_text = 'Unlimited';
            $user->daily_limit = 'Unlimited';
            $user->whitelisted_ips = $user->userDetail->white_listed_ip;
        } else {
            $user->access_type = 'non_whitelisted';
            $user->access_label = 'Open Access';
            $user->access_class = 'label-danger';
            $user->limit_text = '50 SMS/Day';
            $user->daily_limit = 50;
            $user->whitelisted_ips = 'Not Configured';
        }
    }

    return view('admin.whitelistedIp.non_whitelisted_users', compact('users'));
}
   public function getDailyUsage($id)
{
    try {
        $today = Carbon::today()->toDateString();
        
        $usageData = SmsIpDailyLimit::where('user_id', $id)
            ->where('limit_date', $today)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalToday = SmsIpDailyLimit::where('user_id', $id)
            ->where('limit_date', $today)
            ->sum('sms_count');
        
        return response()->json([
            'success' => true,
            'usage_data' => $usageData,
            'total_today' => $totalToday,
            'remaining' => max(0, 50 - $totalToday)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
}
