<?php

namespace App\Http\Controllers\Employee\Flexiload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Model\Operator;
use App\Model\LoadPackage;
use App\Model\EmployeeUser;
use App\Model\LoadCamPending;
use App\Model\LoadCampaign30day;
use App\Model\LoadCampaignId;
use App\Model\AccSmsBalance;
use App\Model\EmployeeUserCommission;
use Carbon\Carbon;

class LoadController extends Controller
{
    public function package_list(){
        $operators = Operator::all();
        $allPackages = LoadPackage::where('status', 1)->get();
       
        // dd($allPackages);
        $packages = $allPackages->groupBy('operator_id');

        return view('employee.flexiload.package-buy', ['operators' => $operators, 'packages' => $packages]);
    }

    public function showPackagesByAjax(Request $request)
    {
        try {
            if (!empty($request->operator) && !empty($request->type)) {
                $packages = LoadPackage::where('operator_id', $request->operator)
                    ->where('package_category', $request->type)
                    ->orderByRaw("CAST(validity as UNSIGNED) ASC")
                    ->orderBy('package_price', 'ASC')
                    ->get();

                return view('employee.ajax._ajax_packages_data', ['packages' => $packages]);

            } elseif (!empty($request->operator) && empty($request->type)) {
                $packages = LoadPackage::where('operator_id', $request->operator)
                    ->orderByRaw("CAST(validity as UNSIGNED) ASC")
                    ->orderBy('package_price', 'ASC')
                    ->get();

                return view('employee.ajax._ajax_packages_data', ['packages' => $packages]);

            } else {
                return response()->json(['code' => 400]); //error code 1
            }
        } catch (\Exception $e) {
            return response()->json(['code' => 401]); //error code 2
        }
    }

    public function packageFormProcess(Request $request)
    {
        // dd($request->all());
        // dd(Auth::guard('employee')->user()->id);
        $validated_data = $request->validate([
            'package_id' => 'required',
            'number_type' => 'required',
            'targeted_number' => 'required',
            // 'flexipin' => 'required',
        ]);


        try {
            // $flexipin = $request->flexipin;
            $package_id = $request->package_id;
            $targeted_number = $request->targeted_number;
            $targeted_number = \PhoneNumber::addNumberPrefix($targeted_number);
            $number_type = $request->number_type;
            $campaign_id = random_int(10, 90) . time() . random_int(1, 9);
            $remarks = $request->remarks;

            $op = \PhoneNumber::checkOperator($targeted_number)->id;
            // dd($op);
            if (!\PhoneNumber::isValid($targeted_number)) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Invalid Number', 'op' => $op]);
            }

            $user = Auth::guard('employee')->user();
            // dd($user);
            $user_balance = \BalanceHelper::getEmployeeBalance($user->id);
            // dd($user_balance);
            $flexiload_price = LoadPackage::where('id', $package_id)->first()->package_price;

            // Checking flexipin
            // if ($user->flexipin != $flexipin) {
            //     return redirect()->back()->with(['type' => 'danger', 'message' => 'Wrong Flexipin !', 'op' => $op]);
            // }elseif ($user->flexipin == NULL) {
            //     return true;
            // }

            // Checking available balance
            // $eligible_amount = $user->flexiload_limit + $flexiload_price;
            if ($flexiload_price >= $user_balance) {
                return redirect()->back()->with(['type' => 'danger', 'message' => 'Insufficient balance !', 'op' => $op]);
            }


            
                DB::beginTransaction();


                $load_campaign = new LoadCamPending();
                $load_campaign->sms_id = Auth::guard('employee')->user()->id.time().random_int(10,99);
                $load_campaign->user_id = $user->create_by;
                if ($request->operator != '') {
                $load_campaign->operator_id = $request->operator;
                }else {
                    $load_campaign->operator_id = \PhoneNumber::getOperatorNameForLoadByNumber($targeted_number);
                }
                $load_campaign->campaign_id = $campaign_id;
                $load_campaign->targeted_number = $targeted_number;
                $load_campaign->owner_name = $request->dynamic_name_column ?? '';
                $load_campaign->package_id = $package_id;
                $load_campaign->number_type = \PhoneNumber::checkOperator($targeted_number)->id == 3 ? 1 : $number_type;
                $load_campaign->campaign_type = '4'; //Package
                $load_campaign->campaign_price = $flexiload_price;
                $load_campaign->remarks = $remarks;
                $load_campaign->status = '0';

                $load_campaign->save();

                // Insert to load campaign ID table
                $campaign = new LoadCampaignId();
                $campaign->user_id = Auth::guard('employee')->user()->id;
                $campaign->campaign_id = $campaign_id;
                $campaign->campaign_name = $request->campaign_name ?? '';
                $campaign->total_number = 1;
                $campaign->total_amount = $flexiload_price;

                $campaign->save();


                /*debit user balance*/
                $user_id = $user->id;

                $user_det = EmployeeUser::where('id', $user_id)->first();
                $current_date = Carbon::now();
               
                    /*get total cost against each reseller*/

                    EmployeeUserCommission::create([
                        'eu_id' => $user_det->id,
                        'eu_ref_id' => 'self load-'.$user_det->id,
                        'euc_credit' => '0',
                        'euc_debit' => $flexiload_price,
                        'euc_status' => '4',//load
                        'created_at' => $current_date,
                
                    ]);
                    
                
                DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something is going Wrong', 'op' => $op]);
        }
        return redirect()->back()->with(['type' => 'success', 'message' => 'Successfully flexiloaded ' . $flexiload_price . ' Tk to ' . $targeted_number, 'op' => $op]);
    }

    public function package_history(){
        // dd(Auth::guard('employee')->user()->id);
        $employee = EmployeeUserCommission::where('eu_id',Auth::guard('employee')->user()->id)->where('euc_status',4)->get();
        // dd($employee);
        // $packages = LoadCampaign30day::select(DB::raw("SUM(campaign_price) as total_price"), DB::raw("count(*) as total_package"), 'operator_id')
        // ->where('user_id', Auth::guard('employee')->user()->id)
        
        // ->where('package_id', '!=', '0')
        // ->groupBy('operator_id')
        // ->get();

        return view('employee.flexiload.package_history', ['employee' => $employee]);
    }

    
}
