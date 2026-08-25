<?php
namespace App\Helpers;
use App\Model\User;
use App\Model\AccSmsBalance;
use App\Model\AccSmsRate;
use App\Model\EmployeeUser;
use App\Model\UserDetail;
use App\Model\SmsCampaign_24h;
use App\Model\LoadCampaign30day;
use Carbon\Carbon;


use App\Model\UserSmsBalance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class BalanceHelper
{

    /*available balance of a user*/
    public static function user_available_balance($user_id)
    {
       $balance = UserSmsBalance::where('user_id', $user_id)->value('balance');

        if ($balance !== null) {
            return (float)$balance;
        }

        // One-time compatibility fallback for users not yet initialized.
        $ledgerBalance = (float)AccSmsBalance::where('asb_pay_to', $user_id)
            ->selectRaw('COALESCE(SUM(asb_credit), 0) - COALESCE(SUM(asb_debit), 0) AS balance')
            ->value('balance');

        UserSmsBalance::firstOrCreate(
            ['user_id' => $user_id],
            ['balance' => $ledgerBalance]
        );

        return $ledgerBalance;
    }

    /*public static function check_parents_available_balance($user_id, $total_cost)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;

            while ($position >= 1) {
                if (self::user_available_balance($parent_id) < $total_cost) {
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                }
            }

            return true;
        }
    }*/

    public static function check_parents_available_balance($user_id, $sms_number, $validUniqueNumbers, $isMasking)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
            while ($position >= 1) {
                $total_cost = self::campaignTotalCost($sms_number, $validUniqueNumbers, $isMasking, $parent_id);
                if ($parent_id == 4 || $parent_id == 5) {
                    $user = DB::table('users')->where('id',$parent_id)->first();
                    

                    if ($user->position == 1) {
//                        return self::user_available_balance($parent_id)." ".$total_cost;
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }else{
                    if (self::user_available_balance($parent_id) < $total_cost) {
//                    return self::user_available_balance($parent_id)." ".$total_cost;
                    return false;
                    } else {
                        $user = DB::table('users')->where('id',$parent_id)->first();
                        

                        if ($user->position == 1) {
    //                        return self::user_available_balance($parent_id)." ".$total_cost;
                            return true;
                        }
                        $position = $user->position;
                        $parent_id = $user->create_by;
                        $user_id = $user->id;
                    }
                }
                
            }

            return true;
        }
    }

    public static function check_dynamic_parents_available_balance($user_id, $valid_numbers, $valid_messages, $isMasking)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
            $validNumbers = $valid_numbers;
            $validMessages = $valid_messages;
            while ($position >= 1) {
                $total_cost = 0;
                $total_sms_number = 0;
                for ($i = 0; $i < count($validNumbers); $i++) {

                    if (\SmsHelper::is_unicode($validMessages[$i])) {
                        $smsType = 'unicode'; //unicode
                        $sms_number = \SmsHelper::unicode_sms_count($validMessages[$i]);

                    } else {
                        $smsType = 'text'; //text
                        $sms_number = \SmsHelper::text_sms_count($validMessages[$i]);
                    }
                    $smsCost = \BalanceHelper::singleSmsCost($sms_number, $validNumbers[$i], $isMasking, Auth::id());
                    $total_cost = $total_cost + $smsCost;
                    $total_sms_number = $total_sms_number + $sms_number;
                }
                if (self::user_available_balance($parent_id) < $total_cost) {
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }
            }

            return true;
        }
    }


    /*paymentable balance of a reseller*/
    public static function reseller_paymentable_balance($reseller_id)
    {
        try {
            $checkReseller = User::with('userDetail')->where(['id' => $reseller_id, 'role' => '4'])->first();
            if ($checkReseller) {
                $reseller_limit = $checkReseller->userDetail->limit;
                $reseller_available_balance = static::user_available_balance($checkReseller->id);
                $reseller_customers = User::where('create_by', $checkReseller->id)->get();
                $reseller_customer_available_balance = 0;
                foreach ($reseller_customers as $reseller_customer) {
                    $reseller_customer_available_balance += static::user_available_balance($reseller_customer->id);
                }
                $reseller_paymentable_balance = ($reseller_limit + $reseller_available_balance) - $reseller_customer_available_balance;

                return $reseller_paymentable_balance;

            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }
/* Get users with low balance (less than threshold) */
public static function getLowBalanceUsers($threshold = 2000, $creator_id = null, $role_filter = null)
{
    try {
        $query = AccSmsBalance::select(
                'asb_pay_to as user_id',
                DB::raw('COALESCE(SUM(asb_credit) - SUM(asb_debit), 0) as balance')
            )
            ->groupBy('asb_pay_to')
            ->having('balance', '<', $threshold)
            ->having('balance', '>', 0); // Only positive balances less than threshold
            
    $lowBalanceUsers = $query->get();
        
        $userIds = $lowBalanceUsers->pluck('user_id')->toArray();
        
        if (empty($userIds)) {
            return collect([]);
        }
        
        // Get user details
        $usersQuery = User::with('userDetail')
            ->whereIn('id', $userIds)
            ->where('status', 1); // Only active users
        
        // Apply role-based filtering
        if ($role_filter == '3') {
            // Role 3 can see all users
            // No additional filter
        } elseif ($creator_id) {
            // Other roles can only see users they created
            $usersQuery->where('create_by', $creator_id);
        }
        
        $users = $usersQuery->get();
        
        // Attach balance to each user
        foreach ($users as $user) {
            $balanceData = $lowBalanceUsers->firstWhere('user_id', $user->id);
            $user->balance = $balanceData ? floatval($balanceData->balance) : 0;
        }
        
        // Filter out any users with balance >= threshold (double-check)
        $users = $users->filter(function($user) use ($threshold) {
            return $user->balance < $threshold && $user->balance > 0;
        });
        
        return $users;
        
    } catch (\Exception $e) {
        \Log::error('Failed to get low balance users', [
            'error' => $e->getMessage(),
            'threshold' => $threshold
        ]);
        return collect([]);
    }
}

    /*user total credit balance*/
    public static function user_total_credit($user_id)
    {
        try {
            $checkUser = User::where(['id' => $user_id])->first();
            if ($checkUser) {
                $customerCredit = AccSmsBalance::where(['asb_pay_to' => $user_id])->sum('asb_credit');
                return $customerCredit;
            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }

    /*user total debit balance*/
    public static function user_total_debit($user_id)
    {
        try {
            $checkUser = User::where(['id' => $user_id])->first();
            if ($checkUser) {
                $customerDebit = AccSmsBalance::where(['asb_pay_to' => $user_id])->sum('asb_debit');
                return $customerDebit;
            }
            return '0';
        } catch (\Exception $e) {
            return '0';
        }
    }


    /*get total cost of a campaign*/
public static function campaignTotalCost($sms_count_number, $numbers, $isMasking, $user_id)
{
    $countOperators = PhoneNumber::countOperator($numbers);
    $totalCost = 0;

 

    foreach ($countOperators as $operatorId => $numberOfOperator) {
        if ($numberOfOperator == 0) {
            continue; // Skip operators with no numbers
        }

        // Determine which rate field to use
        if ($isMasking === true) {
            $rateField = 'asr_masking';
            $rateType = 'masking';
        } elseif ($isMasking === 'iptsp') {
            $rateField = 'asr_nonmasking_iptsp';
            $rateType = 'iptsp';
        } else {
            $rateField = 'asr_nonmasking';
            $rateType = 'nonmasking';
        }

        $sms_rate = AccSmsRate::where([
            'user_id' => $user_id,
            'operator_id' => $operatorId
        ])->first();

        if (!$sms_rate) {
            \Log::error("Missing rate for operator", [
                'operator_id' => $operatorId,
                'rate_type' => $rateType,
                'user_id' => $user_id
            ]);
            continue;
        }

        $rate = $sms_rate->{$rateField} ?? 0;
        $operatorCost = $rate * $numberOfOperator;
        $totalCost += $operatorCost;


    }

    $finalCost = $totalCost * $sms_count_number;

   
    return $finalCost;

//        $totalCost = 0;
//        if ($isMasking == true){
//            $smsRates = AccSmsRate::where('user_id',$user_id)->get();
//            $rate = array();
//            foreach ($smsRates as $smsRate){
//                $rate[$smsRate->operator_id] = $smsRate['asr_masking'];
//            }
//            foreach ($numbers as $number){
//                $operator = PhoneNumber::checkOperator($number);
//                $totalCost = $totalCost + $rate[$operator->id];
//            }
//        }else{
//            $smsRates = AccSmsRate::where('user_id',$user_id)->get();
//            $rate = array();
//            foreach ($smsRates as $smsRate){
//                $rate[$smsRate->operator_id] = $smsRate['asr_nonmasking'];
//            }
//            $ser = 0;
//            foreach ($numbers as $number){
//                $operator = PhoneNumber::checkOperator($number);
//                $totalCost = $totalCost + $rate[$operator['id']];
//                echo $ser++."--".$rate[$operator['id']]."<br>";
//                echo floatval($totalCost)."<br><br>";
//
//            }
//        }
//        $totalCost = $totalCost * $sms_count_number;
//        return $totalCost;
    }


    /*get  cost of a single sms*/
public static function singleSmsCost($sms_count_number, $number, $isMasking, $user_id)
{
    $operator = PhoneNumber::checkOperator($number);
    
    // Determine which rate field to use
    if ($isMasking === true) {
        $rateField = 'asr_masking';
    } elseif ($isMasking === 'iptsp') {
        $rateField = 'asr_nonmasking_iptsp';
    } else {
        $rateField = 'asr_nonmasking';
    }

    // Get the appropriate rate
    $sms_rate = AccSmsRate::select($rateField)
                ->where([
                    'user_id' => $user_id,
                    'operator_id' => $operator->id
                ])->first();

    // Calculate cost
    $cost = $sms_rate->{$rateField};
    $smsCost = $cost * $sms_count_number;

    return $smsCost;
}


    /*add debit balance*/
    public static function add_debit_balance($pay_by, $pay_to, $pay_ref, $debit_balance, $pay_mode, $payment_status, $deal_type)
    {

    }

    // Get employee employee commission ( Credit commission )
    public static function get_employee_commission($user_id, $credit_amount) {
        
        $user = User::find($user_id);

        $employee = $user['employee_user_id'];

        if ( ( !empty($employee)) && ($employee != null)){
            $commission = EmployeeUser::where('id',$employee)->first()->commission;

            $data['commission_amount'] = ( $credit_amount * $commission ) / 100;
            $data['employee_id'] = $employee;

            return $data;
        }else{
            return 0;
        }

    }

    public static function last_transaction_date($user_id) {
        $last_transaction_date = AccSmsBalance::where('asb_pay_to', $user_id)->whereIn('asb_pay_mode', [1,2,3])->orderBy('asb_submit_time', 'desc')->first();
        if(!empty($last_transaction_date)) {
            $last_tr_date = $last_transaction_date->asb_submit_time;
            return $last_tr_date;
        } else {
            return "No Record Found";
        }

    }

    public static function getEmployeeBalance($cId) {
        $total_credit = DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_credit');
        $total_debit = DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_debit');

        return ($total_credit - $total_debit);
    }
    
    public static function getCredit($cId){
        return DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_credit');
    }

    public static function getDebit($cId){
        return DB::table('employee_user_commissions')->where('eu_id', $cId)->sum('euc_debit');
    }
// Flexiload 
    public static function check_flexiload_parent_available_balance($user_id, $flexiload_price)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;

            while ($position >= 1) {
                $parent_user = User::where('id', $parent_id)->first();
                // Calcaulating charge againstst this reseller
                $price_after_commission = $flexiload_price -( ($flexiload_price * $parent_user->flexiload_commission) / 100);

                if (self::user_available_balance($parent_id) < $price_after_commission) {
                    return false;
                } else {
                    // $parent_user = User::where('id', $parent_id)->first();
                    if ($parent_user->position == 1) {
                        return true;
                    }
                    $position = $parent_user->position;
                    $parent_id = $parent_user->create_by;
                }
            }
            return true;
        }
    }

    public static function check_flexiload_employee_available_balance($user_id, $flexiload_price)
    {
        $user = EmployeeUser::where('id', $user_id)->first();
        if ($user->status == 1) {
            return true;
        } else {
            $position = $user->status;
            $parent_id = $user->create_by;

            while ($position >= 1) {
                $parent_user = EmployeeUser::where('id', $parent_id)->first();
                // Calcaulating charge againstst this reseller
                $price_after_commission = $flexiload_price -( ($flexiload_price * $parent_user->flexiload_commission) / 100);

                if (self::user_available_balance($parent_id) < $price_after_commission) {
                    return false;
                } else {
                    // $parent_user = User::where('id', $parent_id)->first();
                    if ($parent_user->status == 1) {
                        return true;
                    }
                    $position = $parent_user->status;
                    $parent_id = $parent_user->create_by;
                }
            }
            return true;
        }
    }

    public static function sms_cost($user_id){
        return SmsCampaign_24h::where('user_id',$user_id)->sum('sct_sms_cost');
    }
    public static function flexi_cost($user_id){
        return LoadCampaign30day::where('user_id',$user_id)->sum('campaign_price');
    }



    public static function campaignDesktopTotalCost($sms_count_number, $numbers, $user_id)
    {
        $countOperators = PhoneNumber::countOperator($numbers);
        $totalCost = 0;
        // if ($isMasking == true) {
        //     foreach ($countOperators as $operatorId => $numberOfOperator) {
        //         /*get user sms rates*/
        //         $sms_rate = AccSmsRate::select('asr_masking')->where(['user_id' => $user_id, 'operator_id' => $operatorId])->first();
        //         $totalCost = $totalCost + ($sms_rate->asr_masking * $numberOfOperator);
        //     }
        // } else {
            foreach ($countOperators as $operatorId => $numberOfOperator) {
                /*get user sms rates*/
                $sms_rate = AccSmsRate::select('asr_dynamic')->where(['user_id' => $user_id, 'operator_id' => $operatorId])->first();
                $totalCost = $totalCost + ($sms_rate->asr_dynamic * $numberOfOperator);
            }
        // }

        $totalCost = $totalCost * $sms_count_number;
        return $totalCost;

//        $totalCost = 0;
//        if ($isMasking == true){
//            $smsRates = AccSmsRate::where('user_id',$user_id)->get();
//            $rate = array();
//            foreach ($smsRates as $smsRate){
//                $rate[$smsRate->operator_id] = $smsRate['asr_masking'];
//            }
//            foreach ($numbers as $number){
//                $operator = PhoneNumber::checkOperator($number);
//                $totalCost = $totalCost + $rate[$operator->id];
//            }
//        }else{
//            $smsRates = AccSmsRate::where('user_id',$user_id)->get();
//            $rate = array();
//            foreach ($smsRates as $smsRate){
//                $rate[$smsRate->operator_id] = $smsRate['asr_nonmasking'];
//            }
//            $ser = 0;
//            foreach ($numbers as $number){
//                $operator = PhoneNumber::checkOperator($number);
//                $totalCost = $totalCost + $rate[$operator['id']];
//                echo $ser++."--".$rate[$operator['id']]."<br>";
//                echo floatval($totalCost)."<br><br>";
//
//            }
//        }
//        $totalCost = $totalCost * $sms_count_number;
//        return $totalCost;
    }

    public static function check_parents_Desktop_available_balance($user_id, $sms_number, $validUniqueNumbers)
    {
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
            while ($position >= 1) {
                $total_cost = self::campaignDesktopTotalCost($sms_number, $validUniqueNumbers, $parent_id);
                if (self::user_available_balance($parent_id) < $total_cost) {
//                    return self::user_available_balance($parent_id)." ".$total_cost;
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
//                        return self::user_available_balance($parent_id)." ".$total_cost;
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }
            }

            return true;
        }
    }


    public static function singleSmsDesktopCost($sms_count_number, $number, $user_id)
    {
        $operator = \PhoneNumber::checkOperator($number);
        // if ($isMasking == true) {
        //     /*get user sms rates*/
        //     $sms_rate = AccSmsRate::select('asr_masking')->where(['user_id' => $user_id, 'operator_id' => $operator->id])->first();
        //     $cost = $sms_rate->asr_masking;
        // } else {
            /*get user sms rates*/
            $sms_rate = AccSmsRate::select('asr_dynamic')->where(['user_id' => $user_id, 'operator_id' => $operator->id])->first();
            $cost = $sms_rate->asr_dynamic;
        // }
        $smsCost = $cost * $sms_count_number;
        return $smsCost;
    }



    public static function check_dynamic_desktop_parents_available_balance($user_id, $valid_numbers, $valid_messages)
    {
        
        $user = User::where('id', $user_id)->first();
        if ($user->position == 1) {
            return true;
        } else {
            $position = $user->position;
            $parent_id = $user->create_by;
            $validNumbers = $valid_numbers;
            $validMessages = $valid_messages;
            while ($position >= 1) {
                $total_cost = 0;
                $total_sms_number = 0;
                for ($i = 0; $i < count($validNumbers); $i++) {
                    $final_text = preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $validMessages[$i]);
                    if (\SmsHelper::is_unicode($validMessages[$i])) {
                        $smsType = 'unicode'; //unicode
                        $sms_number = \SmsHelper::unicode_sms_count($final_text);

                    } else {
                        $smsType = 'text'; //text
                        $sms_number = \SmsHelper::text_sms_count($final_text);
                    }
                    $smsCost = \BalanceHelper::singleSmsDesktopCost($sms_number, $validNumbers[$i], Auth::id());
                    $total_cost = $total_cost + $smsCost;
                    $total_sms_number = $total_sms_number + $sms_number;
                }
                if (self::user_available_balance($parent_id) < $total_cost) {
                    return false;
                } else {
                    $user = User::where('id', $parent_id)->first();

                    if ($user->position == 1) {
                        return true;
                    }
                    $position = $user->position;
                    $parent_id = $user->create_by;
                    $user_id = $user->id;
                }
            }

            return true;
        }
    }


    /**
     * Add a credit ledger entry and update the current balance atomically.
     */
    public static function addCredit(
        $payBy,
        $payTo,
        $reference,
        $amount,
        $payMode = 1,
        $paymentStatus = 1,
        $dealType = 1,
        $targetTime = null,
        $creditReturnType = 0
    )
    {
        $amount = round((float) $amount, 4);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Credit amount must be greater than zero.');
        }

        return DB::transaction(function () use (
            $payBy,
            $payTo,
            $reference,
            $amount,
            $payMode,
            $paymentStatus,
            $dealType,
            $targetTime,
            $creditReturnType
        ) {
            self::initializeCurrentBalance($payTo);

            $currentBalance = UserSmsBalance::where('user_id', $payTo)
                ->lockForUpdate()
                ->firstOrFail();

            AccSmsBalance::create([
                'asb_paid_by' => $payBy,
                'asb_pay_to' => $payTo,
                'asb_pay_ref' => $reference,
                'asb_credit' => $amount,
                'asb_debit' => 0,
                'asb_submit_time' => Carbon::now(),
                'asb_target_time' => $targetTime ?: Carbon::now(),
                'asb_pay_mode' => $payMode,
                'asb_payment_status' => $paymentStatus,
                'asb_deal_type' => $dealType,
                'credit_return_type' => $creditReturnType,
            ]);

            $currentBalance->increment('balance', $amount);

            return (float) $currentBalance->fresh()->balance;
        }, 3);
    }

    /**
     * Add a debit ledger entry and update the current balance atomically.
     */
    public static function addDebit(
        $payBy,
        $payTo,
        $reference,
        $amount,
        $payMode = 4,
        $paymentStatus = 1,
        $dealType = 2,
        $targetTime = null,
        $creditReturnType = 0
    )
    {
        $amount = round((float) $amount, 4);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be greater than zero.');
        }

        return DB::transaction(function () use (
            $payBy,
            $payTo,
            $reference,
            $amount,
            $payMode,
            $paymentStatus,
            $dealType,
            $targetTime,
            $creditReturnType
        ) {
            self::initializeCurrentBalance($payTo);

            $currentBalance = UserSmsBalance::where('user_id', $payTo)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $currentBalance->balance < $amount) {
                throw new \RuntimeException(
                    'Insufficient balance for user ' . $payTo . '.'
                );
            }

            AccSmsBalance::create([
                'asb_paid_by' => $payBy,
                'asb_pay_to' => $payTo,
                'asb_pay_ref' => $reference,
                'asb_credit' => 0,
                'asb_debit' => $amount,
                'asb_submit_time' => Carbon::now(),
                'asb_target_time' => $targetTime ?: Carbon::now(),
                'asb_pay_mode' => $payMode,
                'asb_payment_status' => $paymentStatus,
                'asb_deal_type' => $dealType,
                'credit_return_type' => $creditReturnType,
            ]);

            $currentBalance->decrement('balance', $amount);

            return (float) $currentBalance->fresh()->balance;
        }, 3);
    }

    /**
     * Initialize the current-balance snapshot from the ledger when needed.
     */
    private static function initializeCurrentBalance($userId)
    {
        if (UserSmsBalance::where('user_id', $userId)->exists()) {
            return;
        }

        $ledgerBalance = (float) AccSmsBalance::where('asb_pay_to', $userId)
            ->selectRaw(
                'COALESCE(SUM(asb_credit), 0) - COALESCE(SUM(asb_debit), 0) AS balance'
            )
            ->value('balance');

        UserSmsBalance::firstOrCreate(
            ['user_id' => $userId],
            ['balance' => $ledgerBalance]
        );
    }

}