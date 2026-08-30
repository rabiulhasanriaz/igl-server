<?php

namespace App\Http\Controllers\Api;

use App\Model\LoadCampaignId;
use App\Model\LoadCamPending;
use App\Model\User;
use App\Model\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Helpers\BalanceHelper;
use App\Helpers\PhoneNumber;

class FlexiloadApiController extends Controller
{
    public function send_flexi_load(Request $request)
    {
        if(!$request->api_key){
            return response()->json(['code'=>'445901', 'message'=>'Missing api key']);
        }
        if(!$request->number){
            return response()->json(['code'=>'445902', 'message'=>'Missing Phone Number']);
        }
        if(!$request->number_type){
            return response()->json(['code'=>'445903', 'message'=>'Missing Number Type']);
        }
        if(!$request->operator){
            return response()->json(['code'=>'445904', 'message'=>'Missing Operator']);
        }
        if(!$request->pin){
            return response()->json(['code'=>'445905', 'message'=>'Missing Flexipin']);
        }
        if(!$request->amount){
            return response()->json(['code'=>'445906', 'message'=>'Missing Amount']);
        }

        $request->amount = (int)$request->amount;

        if(($request->amount < 10) || ($request->amount > 50000)) {
            return response()->json(['code'=>'445907', 'message'=>'Amount should be in 10 to 50000']);
        }

        if (!is_integer($request->amount)) {
            return response()->json(['code'=>'445908', 'message'=>'Amount should be a valid integer number']);
        }

        $defined_operators = [
            "gp",
            "gpst",
            "airtel",
            "robi",
            "blink",
            "teletalk"
        ];

        if (!in_array($request->operator,$defined_operators)) {
            return response()->json(['code'=>'445909', 'message'=>'Invalid Operator']);
        }

        try {
            $targeted_number = $request->number;
            $operator = $request->operator;
            $targeted_number = PhoneNumber::addNumberPrefix($targeted_number);
            $number_type = $request->number_type;

            if (!PhoneNumber::isValid($targeted_number)) {
                return response()->json(['code'=>'445910', 'message' => 'Invalid Number']);
            }

            $userDetail = UserDetail::where('flexi_api_key', $request->api_key)
                ->where('flexi_api_key', '!=', NULL)
                ->first();

            if(!$userDetail){
                return response()->json(['code'=>'445911', 'message'=>'Invalid api key']);
            }

            $user = User::where('id', $userDetail->user_id)->first();

            if(($user->flexipin == NULL) || ($user->flexipin != $request->pin)) {
                return response()->json(['code'=>'445912', 'message'=>'Invalid flexipin']);
            }

            $user_balance = BalanceHelper::user_available_balance($user->id);
            $flexiload_price = $request->amount;

            // Checking available balance
            $eligible_amount = $user->flexiload_limit + $flexiload_price;
            if ($eligible_amount >= $user_balance) {
                return response()->json(['code'=>'445913', 'message'=>'Insufficient balance !']);
            }

            $campaign_id = $user->id.random_int(10, 90) . time() . random_int(1, 9);
            $sms_id = $user->id.time().random_int(10,99);

            if (BalanceHelper::check_flexiload_parent_available_balance($user->id, $flexiload_price)) {

                DB::beginTransaction();

                $load_campaign = new LoadCamPending();
                $load_campaign->user_id = $user->id;
                $load_campaign->sms_id = $sms_id;

                if ($request->operator != '') {
                    $load_campaign->operator_id = $request->operator;
                } else {
                    $load_campaign->operator_id = PhoneNumber::getOperatorNameForLoadByNumber($targeted_number);
                }

                $load_campaign->campaign_id = $campaign_id;
                $load_campaign->targeted_number = $targeted_number;
                $load_campaign->owner_name = '';
                $load_campaign->package_id = '0';
                $load_campaign->number_type = $request->number_type;
                $load_campaign->campaign_type = '4'; // api
                $load_campaign->campaign_price = $flexiload_price;
                $load_campaign->status = '0';
                $load_campaign->save();

                // Insert to load campaign ID table
                $campaign = new LoadCampaignId();
                $campaign->user_id = $user->id;
                $campaign->campaign_id = $campaign_id;
                $campaign->campaign_name = '';
                $campaign->total_number = 1;
                $campaign->total_amount = $flexiload_price;
                $campaign->save();

                /*debit user balance*/
                $user_position = $user->position;
                $user_id = $user->id;

                $user_det = User::where('id', $user_id)->first();
                $current_date = Carbon::now();

                while ($user_position >= 1 && $user_det) {
                    /*get total cost against each reseller*/
                    $price_after_commission = $flexiload_price - (($flexiload_price * $user_det->flexiload_commission) / 100);

                    BalanceHelper::addDebit(
                        $user_det->create_by,
                        $user_det->id,
                        $campaign_id,
                        $price_after_commission,
                        5,
                        1,
                        2,
                        $current_date,
                        0
                    );

                    $user_det = User::where('id', $user_det->create_by)->first();

                    if ($user_det) {
                        $user_position = $user_det->position;
                    } else {
                        break;
                    }
                }

                DB::commit();

            } else {
                return response()->json(['code'=>'445914', 'message'=>'Insufficient reseller balance !']);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Flexiload API Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json(['code'=>'445915', 'message'=>'Something went wrong !']);
        }

        $return_data['code'] = "445900";
        $return_data['load_id'] = $sms_id;
        $return_data['message'] = "Load Request Received Successfully";

        return response()->json($return_data);
    }
}
