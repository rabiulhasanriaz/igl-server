<?php

namespace App\Http\Controllers\Auth;

use App\Model\PhonebookCategory;
use App\Model\User;
use App\Model\UserDetail;
use App\Model\SenderIdUserDefault;
use App\Model\SmsCamPending;
use App\Jobs\InsertSms;
use App\Model\AccUserCreditHistory;
use App\Model\PhonebookCampaignCategory;
use App\Model\PhonebookCampaignContact;
use App\Model\PhonebookContact;
use App\Model\SenderIdUser;
use App\Model\SmsCampaignId;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\PhoneNumber;
use App\Helpers\BalanceHelper;

class LoginController extends Controller
{
    public function index()
    {
        $user_domain = $_SERVER['HTTP_HOST'];
        $userInfo = UserDetail::where('domain_name',$user_domain)->first();
        if($userInfo){
            $user_logo = $userInfo->logo;
        }else{
            $user_logo = '../default.png';
        }
        return view('login', compact('user_logo'));
    }

   public function processLogin(Request $request)
{
    // Validate input
    $request->validate([
        'email' => 'required',
        'password' => 'required'
    ]);

    // Check if login is with cellphone or email
    if (is_numeric($request->email)) {
        $getEmail = User::where('cellphone', $request->email)->first();
        if ($getEmail) {
            $email = $getEmail->email;
        } else {
            session()->flash('message', 'Login credential was wrong...');
            return redirect()->back();
        }
    } else {
        $email = $request->email;
    }

    // FIRST: Verify password
    if (Auth::attempt(['email' => $email, 'password' => $request->password])) {

        $user = Auth::user();

        // Check user status
        if ($user->status == '1') {

            // Roles that have IP whitelist feature (1,2,3,4)
            $rolesWithWhitelist = [1, 2, 3, 4];
            $isWhitelistedIp = false;
            $clientIp = $request->ip();
            
            // Check if user role requires IP whitelist check
            if (in_array($user->role, $rolesWithWhitelist)) {
                $userDetail = $user->userDetail;
                $whitelistedIps = $userDetail ? $userDetail->white_listed_ip : null;
                
                // Check if IP is whitelisted
                $isWhitelistedIp = $this->isIpWhitelisted($clientIp, $whitelistedIps);
                
                \Log::info('IP Whitelist Check:', [
                    'user_id' => $user->id,
                    'role' => $user->role,
                    'client_ip' => $clientIp,
                    'whitelisted_ips' => $whitelistedIps,
                    'is_whitelisted' => $isWhitelistedIp
                ]);
            }

            // Get otp_check from user_details (default 0 if null)
            $otpCheck = optional($user->userDetail)->otp_check ?? 0;
            
            // Check if user has OTP disabled in settings
            $hasOtpDisabled = ($otpCheck == 1);
            
            // FINAL SKIP LOGIC:
            // Skip OTP if:
            // 1. User role is 1-4 AND IP is whitelisted, OR
            // 2. User has otp_check = 1 in user_details
            $skipOtp = false;
            
            if (in_array($user->role, $rolesWithWhitelist)) {
                // For roles 1-4: Skip OTP if IP is whitelisted OR OTP is disabled in settings
                if ($isWhitelistedIp || $hasOtpDisabled) {
                    $skipOtp = true;
                    \Log::info('OTP Skipped for role ' . $user->role . ': ' . ($isWhitelistedIp ? 'IP whitelisted' : 'OTP disabled'));
                }
            } else {
                // For other roles: Skip OTP only if OTP is disabled in settings
                if ($hasOtpDisabled) {
                    $skipOtp = true;
                    \Log::info('OTP Skipped: OTP disabled in settings');
                }
            }

            if ($skipOtp) {
                // Direct login without OTP
                $user->login_status = 1;
                $user->last_login_time = now();
                $user->last_active_time = now();
                $user->save();

                return redirect('/home');
            }

            // -----------------------
            // OTP REQUIRED USERS (only reaches here if $skipOtp = false)
            // -----------------------
            \Log::info('OTP Required for user: ' . $user->id . ', role: ' . $user->role);
            
            $otpCode = rand(100000, 999999);
            $expireTime = Carbon::now()->addMinutes(5);

            session([
                'otp_code' => $otpCode,
                'otp_expire' => $expireTime,
                'otp_user_id' => $user->id
            ]);

            $otpSent = $this->sendOtpViaSmsSystem($user, $otpCode);

            Auth::logout();

            if (!$otpSent) {
                // Check if there's a specific error message from sendOtpViaSmsSystem
                $errorMessage = session('otp_error_message');
                if ($errorMessage) {
                    session()->flash('message', $errorMessage);
                    session()->forget('otp_error_message');
                } else {
                    session()->flash('message', 'Failed to send OTP. Please try again.');
                }
                return redirect()->back();
            }

            session()->flash('message', 'OTP sent to your mobile number. Please verify.');
            return redirect()->route('auth.otp.verify');

        } elseif ($user->status == '2') {
            Auth::logout();
            session()->flash('message', 'Your account was suspended');
            return redirect()->back();

        } else {
            Auth::logout();
            session()->flash('message', 'Your account was expired');
            return redirect()->back();
        }

    } else {
        session()->flash('message', 'Login credential was wrong...');
        return redirect()->back();
    }
}

    /**
     * Check if client IP is whitelisted
     * 
     * @param string $clientIp
     * @param string|null $whitelistedIps
     * @return bool
     */
   /**
 * Check if client IP is whitelisted
 * 
 * @param string $clientIp
 * @param string|null $whitelistedIps
 * @return bool
 */
private function isIpWhitelisted($clientIp, $whitelistedIps)
{
    // If no whitelist is configured, return false (not whitelisted)
    if (empty($whitelistedIps)) {
        \Log::info('No whitelist configured');
        return false;
    }
    
    // Split whitelisted IPs by comma or new line
    $ips = preg_split('/[\s,]+/', $whitelistedIps);
    
    \Log::info('Checking IP: ' . $clientIp . ' against: ' . json_encode($ips));
    
    foreach ($ips as $whitelistedIp) {
        $whitelistedIp = trim($whitelistedIp);
        
        // Skip empty entries
        if (empty($whitelistedIp)) {
            continue;
        }
        
        // Check for exact match
        if ($clientIp === $whitelistedIp) {
            \Log::info('IP matched exactly: ' . $clientIp);
            return true;
        }
        
        // Check for CIDR notation (e.g., 192.168.1.0/24)
        if (strpos($whitelistedIp, '/') !== false) {
            if ($this->ipInCidr($clientIp, $whitelistedIp)) {
                \Log::info('IP matched CIDR: ' . $clientIp . ' in ' . $whitelistedIp);
                return true;
            }
        }
        
        // Check for wildcard notation (e.g., 192.168.1.*)
        if (strpos($whitelistedIp, '*') !== false) {
            $pattern = str_replace('*', '[0-9]+', $whitelistedIp);
            $pattern = str_replace('.', '\.', $pattern);
            if (preg_match('/^' . $pattern . '$/', $clientIp)) {
                \Log::info('IP matched wildcard: ' . $clientIp);
                return true;
            }
        }
    }
    
    \Log::info('IP not whitelisted: ' . $clientIp);
    return false;
}
    
    /**
     * Check if an IP falls within a CIDR range
     * 
     * @param string $clientIp
     * @param string $cidr
     * @return bool
     */
    private function ipInCidr($clientIp, $cidr)
    {
        list($subnet, $mask) = explode('/', $cidr);
        
        if ((ip2long($clientIp) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet)) {
            return true;
        }
        
        return false;
    }

    private function sendOtpViaSmsSystem($user, $otpCode)
    {
        try {
            // ----------------------------
            // FIXED SETTINGS
            // ----------------------------
            $senderId = 156;

            // FORCE MASKING TYPE
            $sms_masking_type = '2';

            // FIXED SMS COST
            $single_sms_cost = 0.50;

            // ----------------------------
            // OTP MESSAGE
            // ----------------------------
            $message = "Your OTP code for login is: " . $otpCode .
                ".\nThis code will expire in 5 minutes.\nwww.FelnaTech.com";

            $cellphone = $user->cellphone;

            // ----------------------------
            // CHECK SMS TYPE
            // ----------------------------
            $isUnicode = \SmsHelper::is_unicode($message);

            if ($isUnicode) {
                $sms_number = \SmsHelper::unicode_sms_count($message);
                $smsType = 'unicode';
            } else {
                $sms_number = \SmsHelper::text_sms_count($message);
                $smsType = 'text';
            }

            // ----------------------------
            // VALIDATE PHONE NUMBER
            // ----------------------------
            $validNumbers = [];
            $number = PhoneNumber::addNumberPrefix($cellphone);
            
            if (PhoneNumber::isValid($number)) {
                $validNumbers[] = $number;
            }

            if (count($validNumbers) < 1) {
                session(['otp_error_message' => 'Invalid phone number format.']);
                return false;
            }

            $validUniqueNumbers = array_unique($validNumbers);

            // ----------------------------
            // FIXED COST CALCULATION
            // ----------------------------
            $total_sms_number = $sms_number * count($validUniqueNumbers);
            $total_cost = $single_sms_cost * $total_sms_number;

            // ----------------------------
            // USER BALANCE CHECK
            // ----------------------------
            $userBalance = BalanceHelper::user_available_balance($user->id);
            
            if ($userBalance < $total_cost) {
                session(['otp_error_message' => 'Insufficient balance. Please recharge your account.']);
                return false;
            }

            // ----------------------------
            // CAMPAIGN ID
            // ----------------------------
            $campaign_id = $user->id . time() . random_int(11111, 99999);
            $target_time = Carbon::now();

            // ----------------------------
            // BROWSER INFO
            // ----------------------------
            $browser_info = \SmsHelper::getBrowser();
            $br = $browser_info['name'] . " | " . $browser_info['version'];
            $os = \SmsHelper::os_info($_SERVER['HTTP_USER_AGENT']);
            $br .= ' | ' . $os;

            // ----------------------------
            // CREATE CAMPAIGN
            // ----------------------------
            $insertCampaign = SmsCampaignId::create([
                'user_id' => $user->id,
                'sender_id' => $senderId,
                'sci_campaign_id' => $campaign_id,
                'sci_total_submitted' => $total_sms_number,
                'sci_total_cost' => $total_cost,
                'sci_campaign_type' => '1',
                'sci_deal_type' => '1',
                'sci_sms_type' => $sms_masking_type,
                'sci_dynamic_type' => '0',
                'sci_targeted_time' => $target_time,
                'sci_browser' => $br,
                'sci_mac_address' => null,
                'sci_ip_address' => request()->ip()
            ]);

            // ----------------------------
            // INSERT PENDING SMS
            // ----------------------------
            foreach ($validUniqueNumbers as $number) {
                $operator = PhoneNumber::checkOperator($number);
                
                SmsCamPending::create([
                    'user_id' => $user->id,
                    'sender_id' => $senderId,
                    'campaign_id' => $insertCampaign->id,
                    'scp_cell_no' => $number,
                    'scp_message' => $message,
                    'scp_sms_cost' => $single_sms_cost,
                    'operator_id' => $operator['id'],
                    'scp_campaign_type' => '1',
                    'scp_deal_type' => '1',
                    'scp_sms_type' => $sms_masking_type,
                    'scp_sms_id' => '0',
                    'scp_tried' => '0',
                    'scp_picked' => '0',
                    'scp_sms_text_type' => $smsType,
                    'scp_target_time' => $target_time,
                    'scp_status' => '1',
                ]);
            }

            // ----------------------------
            // DEBIT USER BALANCE
            // ----------------------------
            $user_position = $user->position;
            $user_det = User::where('id', $user->id)->first();

            while ($user_position >= 1) {
                BalanceHelper::addDebit(
                    $user_det->create_by,
                    $user_det->id,
                    $campaign_id,
                    $total_cost,
                    4,
                    1,
                    2,
                    $target_time,
                    0
                );

                $user_det = User::where('id', $user_det->create_by)->first();
                if ($user_det) {
                    $user_position = $user_det->position;
                } else {
                    break;
                }
            }

            // ----------------------------
            // CREDIT HISTORY
            // ----------------------------
            AccUserCreditHistory::create([
                'campaign_id' => $insertCampaign->id,
                'user_id' => $user->id,
                'uch_sms_count' => $total_sms_number,
                'uch_sms_cost' => $total_cost,
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('OTP SMS sending failed: ' . $e->getMessage());
            session(['otp_error_message' => 'Failed to send OTP. Please try again.']);
            return false;
        }
    }

    public function otpForm()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('auth.login');
        }
        
        return view('verify_otp');
    }

    public function resendOtp(Request $request)
    {
        if (!session()->has('otp_user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.'
            ]);
        }
        
        $userId = session('otp_user_id');
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }
        
        // Generate new OTP
        $otpCode = rand(100000, 999999);
        $expireTime = Carbon::now()->addMinutes(5);
        
        // Update session
        session([
            'otp_code' => $otpCode,
            'otp_expire' => $expireTime,
            'otp_user_id' => $userId
        ]);
        
        // Send new OTP via SMS
        $otpSent = $this->sendOtpViaSmsSystem($user, $otpCode);
        
        if ($otpSent) {
            return response()->json([
                'success' => true,
                'message' => 'New OTP sent to your phone.',
                'otp_expire' => $expireTime->toDateTimeString()
            ]);
        } else {
            // Check if there's a specific error message
            $errorMessage = session('otp_error_message');
            if ($errorMessage) {
                session()->forget('otp_error_message');
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ]);
        }
    }

    public function checkOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required'
        ]);
        
        if (!session()->has('otp_code')) {
            return redirect()->route('auth.login')->with('message', 'Session expired');
        }
        
        // Expiry check
        if (now()->gt(session('otp_expire'))) {
            session()->forget(['otp_code', 'otp_expire', 'otp_user_id', 'otp_balance_cut']);
            
            return redirect()->route('auth.login')
                ->with('message', 'OTP expired. Please login again.');
        }
        
        // Match OTP
        if ($request->otp != session('otp_code')) {
            return back()->with('message', 'Invalid OTP');
        }
        
        // LOGIN SUCCESS
        $user = User::find(session('otp_user_id'));
        
        Auth::login($user);
        
        $user->login_status = 1;
        $user->last_login_time = now();
        $user->last_active_time = now();
        $user->save();
        
        // Clear session
        session()->forget(['otp_code', 'otp_expire', 'otp_user_id', 'otp_balance_cut']);
        
        return redirect('/home');
    }

    public function logout()
    {
        if(Auth::check()) {
            // Deactivating Login status
            $user = Auth::user();
            $user->login_status = 0;
            $user->save();

            if (Auth::user()->role != '5') {
                $logout_url = Auth::user()->userDetail['logout_url'];
            } else {
                $userParent = User::where('id', Auth::user()->create_by)->first();
                $logout_url = $userParent->userDetail['logout_url'];
            }

            Auth::logout();
            if ($logout_url != null) {
                return redirect($logout_url);
            } else {
                return redirect()->back();
            }
        }else{
            return redirect()->back();
        }

        if(Auth::guard('employee')->check()){
            Auth::guard('employee')->logout();
        }
    }

    /*forgot password*/
  public function forgotPassword(Request $request)
{
    $request->validate([
        'verification_number' => 'required',
    ]);

    // Remove all non-digit characters
    $number = preg_replace('/\D/', '', $request->verification_number);

    // Normalize number
    if (preg_match('/^8801[0-9]{9}$/', $number)) {
        $number = substr($number, 2);
    } elseif (!preg_match('/^01[0-9]{9}$/', $number)) {
        return back()->with('message', 'Invalid mobile number.');
    }

    // Find user
    $user = User::with('userDetail')
        ->where('cellphone', $number)
        ->first();

    if (!$user) {
        return back()->with('message', 'Account not found.');
    }

    if (!$user->userDetail) {
        return back()->with('message', 'User details not found.');
    }

    if (empty($user->userDetail->user_p)) {
        return back()->with('message', 'Password not found.');
    }

    // ----------------------------
    // SAME PATTERN AS OTP - START
    // ----------------------------
    try {
        // FIXED SETTINGS (SAME AS OTP)
        $senderId = 156;
        $sms_masking_type = '2';
        $single_sms_cost = 0.50;

        // FORGOT PASSWORD MESSAGE
        $message = "Your password is: " . $user->userDetail->user_p;

        $cellphone = $user->cellphone;

        // CHECK SMS TYPE (SAME AS OTP)
        $isUnicode = \SmsHelper::is_unicode($message);

        if ($isUnicode) {
            $sms_number = \SmsHelper::unicode_sms_count($message);
            $smsType = 'unicode';
        } else {
            $sms_number = \SmsHelper::text_sms_count($message);
            $smsType = 'text';
        }

        // VALIDATE PHONE NUMBER (SAME AS OTP)
        $validNumbers = [];
        $number = PhoneNumber::addNumberPrefix($cellphone);

        if (PhoneNumber::isValid($number)) {
            $validNumbers[] = $number;
        }

        if (count($validNumbers) < 1) {
            return back()->with('message', 'Invalid phone number format.');
        }

        $validUniqueNumbers = array_unique($validNumbers);

        // COST CALCULATION (SAME AS OTP)
        $total_sms_number = $sms_number * count($validUniqueNumbers);
        $total_cost = $single_sms_cost * $total_sms_number;

        // ----------------------------
        // BALANCE CHECK - SHOWS ERROR IF INSUFFICIENT
        // ----------------------------
        $userBalance = BalanceHelper::user_available_balance($user->id);
        
        \Log::info('Forgot Password Balance Check:', [
            'user_id' => $user->id,
            'balance' => $userBalance,
            'cost' => $total_cost
        ]);

        if ($userBalance < $total_cost) {
            // This will show: "Insufficient balance. Please recharge your account."
            return back()->with('message', 'Insufficient balance. Please recharge your account.');
        }

        // CAMPAIGN ID (SAME AS OTP)
        $campaign_id = $user->id . time() . random_int(11111, 99999);
        $target_time = Carbon::now();

        // BROWSER INFO (SAME AS OTP)
        $browser_info = \SmsHelper::getBrowser();
        $br = $browser_info['name'] . " | " . $browser_info['version'];
        $os = \SmsHelper::os_info($_SERVER['HTTP_USER_AGENT']);
        $br .= ' | ' . $os;

        // CREATE CAMPAIGN (SAME AS OTP)
        $insertCampaign = SmsCampaignId::create([
            'user_id' => $user->id,
            'sender_id' => $senderId,
            'sci_campaign_id' => $campaign_id,
            'sci_total_submitted' => $total_sms_number,
            'sci_total_cost' => $total_cost,
            'sci_campaign_type' => '1',
            'sci_deal_type' => '1',
            'sci_sms_type' => $sms_masking_type,
            'sci_dynamic_type' => '0',
            'sci_targeted_time' => $target_time,
            'sci_browser' => $br,
            'sci_mac_address' => null,
            'sci_ip_address' => request()->ip()
        ]);

        // INSERT PENDING SMS (SAME AS OTP)
        foreach ($validUniqueNumbers as $number) {
            $operator = PhoneNumber::checkOperator($number);
            
            SmsCamPending::create([
                'user_id' => $user->id,
                'sender_id' => $senderId,
                'campaign_id' => $insertCampaign->id,
                'scp_cell_no' => $number,
                'scp_message' => $message,
                'scp_sms_cost' => $single_sms_cost,
                'operator_id' => $operator['id'],
                'scp_campaign_type' => '1',
                'scp_deal_type' => '1',
                'scp_sms_type' => $sms_masking_type,
                'scp_sms_id' => '0',
                'scp_tried' => '0',
                'scp_picked' => '0',
                'scp_sms_text_type' => $smsType,
                'scp_target_time' => $target_time,
                'scp_status' => '1',
            ]);
        }

        // DEBIT USER BALANCE (SAME AS OTP)
        $user_position = $user->position;
        $user_det = User::where('id', $user->id)->first();

        while ($user_position >= 1) {
            BalanceHelper::addDebit(
                $user_det->create_by,
                $user_det->id,
                $campaign_id,
                $total_cost,
                4,
                1,
                2,
                $target_time,
                0
            );

            $user_det = User::where('id', $user_det->create_by)->first();
            if ($user_det) {
                $user_position = $user_det->position;
            } else {
                break;
            }
        }

        // CREDIT HISTORY (SAME AS OTP)
        AccUserCreditHistory::create([
            'campaign_id' => $insertCampaign->id,
            'user_id' => $user->id,
            'uch_sms_count' => $total_sms_number,
            'uch_sms_cost' => $total_cost,
        ]);

        return back()->with('message', 'Password has been sent successfully.');

    } catch (\Exception $e) {
        \Log::error('Forgot Password Error: ' . $e->getMessage());
        return back()->with('message', 'An error occurred. Please try again.');
    }
    // ----------------------------
    // SAME PATTERN AS OTP - END
    // ----------------------------
}

    public function update_login_status(Request $request)
    {
        if(Auth::check()) {
            $user = Auth::user();

            if ($user->last_active_time > Carbon::now()->subMinute()) {
                if ($request->currentSecond <= 300) {
                    $user->login_status = 1;
                    $user->last_active_time = Carbon::now();
                    $user->save();
                }
            } else {
                if ($request->currentSecond > 300) {
                    $user->login_status = 2;
                } else {
                    $user->login_status = 1;
                }
                $user->last_active_time = Carbon::now();
                $user->save();
            }
        }
        return response()->json(['code'=>200]);
    }

    public function maintenance(){
        return view('maintain');
    }
}
