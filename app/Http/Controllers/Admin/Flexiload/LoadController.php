<?php

namespace App\Http\Controllers\Admin\Flexiload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use App\Model\User;
use App\Model\LoadPackage;
use App\Model\Operator;
use App\Model\LoadCampaign30day;
use App\Model\LoadSimMessages;
use App\Model\LoadCamPending;
use App\Model\SmsCampaign;
use App\Model\LoadSimAvailablleBalance;
use App\Model\LoadCampaignId;

class LoadController extends Controller
{
    public function index()
    {
        $users = User::with('userDetail')->where('role', 5)->get();
        return view('admin.flexiload.index', ['users' => $users]);
    }

    public function customizeLoadInfo(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'user_id' => 'required',
            'limit_amount' => 'required',
        ]);

        try {
            if (!empty($request->load_access)) {
                $new_access = implode("-", $request->load_access);
            } else {
                $new_access = 0;
            }
            //dd($new_access);
            $user = User::find($request->user_id);
            $user->flexiload_type = $new_access;
            $user->flexiload_limit = $request->limit_amount;

            $user->save();

            return redirect()->back()->with(['type' => 'success', 'message' => 'Successfully Updated']);
        } catch (\Exception $e) {
            //dd($e->getMessage());
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something Wrong !' . $e]);
        }
    }

    public function makeActiveInactive(Request $request)
    {
        $id = (int)$request->id;
        $package = LoadPackage::find($id);
        $package->status = abs($package->status - 1);

        $package->save();

        return redirect()->back()->with(['type' => 'success', 'message' => 'Package status changed successfully']);

    }


    public function addPackage(Request $request)
    {
        $validated_data = $request->validate([
            'package_category' => 'required|numeric|min:1|max:4',
            'operator' => 'required',
            'package_price' => 'required',
            'package_details' => 'required',
            'commission' => 'numeric',
            'validity' => 'required',
        ]);
        try {
            $package_category = $request->package_category;
            $operator = $request->operator;
            $package_price = $request->package_price;
            $package_details = $request->package_details;
            $commission = $request->commission;
            $validity = $request->validity;

            $package = new LoadPackage();

            $package->package_category = $package_category;
            $package->operator_id = $operator;
            $package->package_price = $package_price;
            $package->package_details = $package_details;
            $package->commission = $commission;
            $package->validity = $validity;
            $package->status = 1;

            $package->save();

            return redirect()->back()->with(['type' => 'success', 'message' => 'Package Added succesfully']);

        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something wrong !']);
        }
    }

    public function editPackage(Request $request)
    {

        $validated_data = $request->validate([
            'package_id' => 'required|numeric',
            'e_package_category' => 'required|numeric|min:1|max:4',
            'e_operator' => 'required',
            'e_package_price' => 'required',
            'e_package_details' => 'required',
            'e_commission' => 'required',
            'e_validity' => 'required',
        ]);

        try {
            $package_category = $request->e_package_category;
            $operator = $request->e_operator;
            $package_price = $request->e_package_price;
            $package_details = $request->e_package_details;
            $commission = $request->e_commission;
            $validity = $request->e_validity;

            $package = LoadPackage::find($request->package_id);

            $package->package_category = $package_category;
            $package->operator_id = $operator;
            $package->package_price = $package_price;
            $package->package_details = $package_details;
            $package->commission = $commission;
            $package->validity = $validity;

            $package->save();

            return redirect()->back()->with(['type' => 'success', 'message' => 'Package Updated successfully']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something is going wrong !']);
        }
    }

    public function setComissionsView()
    {
        $resellers = User::where('role', 4)->get();
        return view("admin.flexiload.resellersCommisions", ['resellers' => $resellers]);
    }

    public function setComissions(Request $request)
    {
        //dd($request->all());
        try {
            $reseller = User::find($request->reseller_id);
            $commission = $request->comission;

            $reseller->flexiload_commission = $commission;
            $reseller->save();

            return redirect()->back()->with(['type' => 'success', 'message' => 'Comission Updated successfully']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'danger', 'message' => 'Something wrong' . $e]);
        }

    }


    public function viewAllPackages()
    {
        $operators = Operator::take(5)->get();
        $packages = LoadPackage::all();
        return view('admin.flexiload.allPackages', ['operators' => $operators, 'packages' => $packages]);
    }

    public function set_trx_id_page()
    {
        $load_trx = LoadCamPending::where('status', 1)
            ->where(function ($query) {
                $query->where('transaction_id', NULL)
                    ->orWhere('transaction_id', '');

            })
            ->get();
        $load = LoadCamPending::where('status', 1)
            ->where(function ($query) {
                $query->where('transaction_id', NULL)
                    ->orWhere('transaction_id', '');

            })
            ->pluck('id')->toArray();
        $loa = implode(',',$load);
        $gp = LoadCamPending::where('status', 1)
                                ->where(function ($query) {
                                    $query->where('transaction_id', NULL)
                                        ->orWhere('transaction_id', '');

                                })
                                ->where('operator_id','gp')->sum('campaign_price');
        $robi = LoadCamPending::where('status', 1)
        ->where(function ($query) {
            $query->where('transaction_id', NULL)
                ->orWhere('transaction_id', '');

        })
        ->where('operator_id','robi')->sum('campaign_price');
        $bl = LoadCamPending::where('status', 1)
                            ->where(function ($query) {
                                $query->where('transaction_id', NULL)
                                    ->orWhere('transaction_id', '');

                            })
                            ->where('operator_id','blink')->sum('campaign_price');
        $airtel = LoadCamPending::where('status', 1)
        ->where(function ($query) {
            $query->where('transaction_id', NULL)
                ->orWhere('transaction_id', '');

        })
        ->where('operator_id','airtel')->sum('campaign_price');
        $tt = LoadCamPending::where('status', 1)
                            ->where(function ($query) {
                                $query->where('transaction_id', NULL)
                                    ->orWhere('transaction_id', '');

                            })
                            ->where('operator_id','teletalk')->sum('campaign_price');
        $total = $gp + $robi + $bl + $airtel + $tt;
        return view('admin.flexiload.set_trx_id', compact('load_trx','loa','gp','robi','bl','airtel','tt','total'));
    }

    public function update_trx_id(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $loadCamPending = LoadCamPending::where('id', $id)
                ->first();
//        update(['transaction_id' => $request->trx_id_set])
            if (!empty($loadCamPending)) {
                $loadCamPending->transaction_id = $request->trx_id_set;
                $loadCamPending->save();

                $loadCamPending->sms_id = $loadCamPending->id;
                $loadCamPending->id = null;
//                dd(json_decode($loadCamPending, true));
                $succesfull_load = new LoadCampaign30day();
                $succesfull_load->create(json_decode($loadCamPending, true));
                $loadCamPending->delete();
                DB::commit();
                return redirect()->back()->with(['update' => 'Transaction ID Updated Successfully']);
            } else {
                DB::rollback();

                return redirect()->back()->with(['update' => 'Invalid Pending Data']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['update' => 'Something went wrong']);

        }
    }

    public function reload_all(Request $request){
       
        try {
            DB::beginTransaction();
            
            $pending = LoadCamPending::where('status', 1)->whereIn('id',$request->trx_pending)->get();
            // dd($pending);
            if (!empty($pending)) {
                LoadCamPending::whereIn('id',$pending->pluck('id'))->update(['status' => 0]);

                DB::commit();
                return redirect()->back()->with(['update' => 'This load will be execute again']);
            }else {
                DB::rollback();
                return redirect()->back()->with(['update' => 'Invalid Pending Data']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['update' => $e->getMessage()]);
        }
       
    }

    public function reload_load(Request $request,$id)
    {
        try {
            DB::beginTransaction();

            $loadCamPending = LoadCamPending::where('id', $id)
                ->first();
            if (!empty($loadCamPending)) {
                $loadCamPending->number_type = $request->num_type;
                $loadCamPending->status = 0;
                $loadCamPending->save();

                DB::commit();
                return redirect()->back()->with(['update' => 'This load will be execute again']);
            } else {
                DB::rollback();

                return redirect()->back()->with(['update' => 'Invalid Pending Data']);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with(['update' => 'Something went wrong']);

        }

    }



    public function balance_enquiry()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(7);

        $flexiHistory = LoadCampaignId::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [0, 1])
            ->select('user_id', 'campaign_id', 'total_number', 'total_amount')
            ->orderBy('created_at', 'asc')
            ->get();

        $numberSum = LoadCampaignId::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [0, 1])
            ->select('user_id', 'total_number')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('user_id')
            ->map(function ($userCampaigns) {
                return $userCampaigns->sum('total_number');
            });

        $numPendSum = LoadCamPending::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [0, 1])
            ->select('user_id')
            ->get()
            ->groupBy('user_id')
            ->map(function ($userGroup) {
                return $userGroup->count();
            });

        $numPendAmount = LoadCamPending::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', [0, 1])
            ->select('user_id', 'campaign_price')
            ->get()
            ->groupBy('user_id')
            ->map(function ($userGroup) {
                return $userGroup->sum('campaign_price');
            });

        $userNames = User::whereIn('id', $flexiHistory->pluck('user_id'))->pluck('company_name', 'id');
        
        $campaignPriceSum = $flexiHistory->groupBy('user_id')
            ->map(function ($userHistory) {
                return $userHistory->sum('total_amount');
            });

        $latestbal = LoadSimAvailablleBalance::where('status', 1)->first();

        $pending_bal_pre = LoadCamPending::where('number_type', 1)
            ->whereIn('status', [0, 1])
            ->sum('campaign_price');
        $pending_bal_post = LoadCamPending::where('number_type', 2)
            ->whereIn('status', [0, 1])
            ->sum('campaign_price');

        return view('admin.flexiload.balance', compact('latestbal', 'campaignPriceSum','numPendSum', 'numberSum', 'pending_bal_pre', 'pending_bal_post','userNames','numPendAmount'));
    }





    public function load_message()
    {
        $last30 = Carbon::now()->subDays(30);
        $last7 = Carbon::now()->subDays(7);

        $messages = LoadSimMessages::where('status', 1)
            ->where('created_at', '>=', $last7)
            ->orderBy('created_at', 'desc')
            ->get();

        $grameen = LoadSimMessages::where('operator_company', 'gp')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        $blink = LoadSimMessages::where('operator_company', 'blink')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        $airtel = LoadSimMessages::where('operator_company', 'airtel')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        $robi = LoadSimMessages::where('operator_company', 'robi')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();
        $teletalk = LoadSimMessages::where('operator_company', 'teletalk')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();


        $pendings = LoadCamPending::where('status', [0,1])->get();

        $gp_operator = LoadSimMessages::where('operator_company', 'gp')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->count();
        $bl_operator = LoadSimMessages::where('operator_company', 'blink')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->count();
        $airtel_operator = LoadSimMessages::where('operator_company', 'airtel')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->count();
        $robi_operator = LoadSimMessages::where('operator_company', 'robi')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->count();
        $tt_operator = LoadSimMessages::where('operator_company', 'teletalk')
            ->where('created_at', '>=', $last7)
            ->where('status', 1)
            ->count();


        $gp_pending = LoadCamPending::where('operator_id', 'gp')
            ->where('status', [0,1])
            ->count();
        $bl_pending = LoadCamPending::where('operator_id', 'blink')
            ->where('status', [0,1])
            ->count();
        $airtel_pending = LoadCamPending::where('operator_id', 'airtel')
            ->where('status', [0,1])
            ->count();
        $robi_pending = LoadCamPending::where('operator_id', 'robi')
            ->where('status', [0,1])
            ->count();
        $tt_pending = LoadCamPending::where('operator_id', 'teletalk')
            ->where('status', [0,1])
            ->count();

        $gp_pending_bal = LoadCamPending::where('operator_id', 'gp')
                                    ->where('status', [0,1])
                                    ->sum('campaign_price');
        $bl_pending_bal = LoadCamPending::where('operator_id', 'blink')
                                        ->where('status', [0,1])
                                        ->sum('campaign_price');
        $airtel_pending_bal = LoadCamPending::where('operator_id', 'airtel')
                                            ->where('status', [0,1])
                                            ->sum('campaign_price');
        $robi_pending_bal = LoadCamPending::where('operator_id', 'robi')
                                        ->where('status', [0,1])
                                        ->sum('campaign_price');
        $tt_pending_bal = LoadCamPending::where('operator_id', 'teletalk')
                                        ->where('status', [0,1])
                                        ->sum('campaign_price');
        // foreach ($messages as $message) {
        //     return $msg_num = substr($message->message,74,-74);
        // }
        return view('admin.flexiload.load-message', compact('messages',
            'pendings',
            'gp_operator',
            'bl_operator',
            'airtel_operator',
            'robi_operator',
            'tt_operator',
            'gp_pending',
            'bl_pending',
            'airtel_pending',
            'robi_pending',
            'tt_pending',
            'grameen',
            'robi',
            'blink',
            'airtel',
            'teletalk',
            'gp_pending_bal',
            'bl_pending_bal',
            'airtel_pending_bal',
            'robi_pending_bal',
            'tt_pending_bal'));
    }

    public function operator_reports(Request $request){
            // dd($request->operator_id);
            // $date = Carbon::now()->subDays(7);
            // dd($date);
            
            $sms_report = SmsCampaign::with('operator')->select(DB::raw('count(*) as total,operator_id'),DB::raw('sum(sc_sms_cost) as total_cost'))
                                     ->where('created_at','>=',Carbon::now()->subDays(30))
                                     ->groupBy('operator_id')
                                     ->get();
            $flexi_report = LoadCampaign30day::select(DB::raw('count(*) as total,operator_id'),DB::raw('sum(campaign_price) as total_cost'))
                                    ->groupBy('operator_id')
                                    ->get();
                                    //  dd($total);
            return view('admin.operator_reports',compact('sms_report','flexi_report'));
        }






  /**
     * User-wise Load History with Total Amount
     */
  public function userWiseLoadHistory(Request $request)
{
    $users = User::where('role', 5)->get();
    
    $query = LoadCampaign30day::with(['trx_user', 'package_info'])
        ->select([
            'user_id',
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(campaign_price) as total_amount'),
            DB::raw('COUNT(DISTINCT targeted_number) as unique_numbers')
        ])
        ->groupBy('user_id');

    // User filter
    if ($request->has('user_id') && $request->user_id) {
        $query->where('user_id', $request->user_id);
    }

    // Date filter - Fixed date format handling
    if ($request->has('from_date') && $request->from_date) {
        $fromDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->from_date)->startOfDay();
        $query->where('created_at', '>=', $fromDate);
    }
    if ($request->has('to_date') && $request->to_date) {
        $toDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->to_date)->endOfDay();
        $query->where('created_at', '<=', $toDate);
    }

    $userWiseHistory = $query->get();

    return view('admin.flexiload.user-wise-history', [
        'userWiseHistory' => $userWiseHistory,
        'users' => $users
    ]);
}

    /**
     * Number-wise Load History with Total Amount
     */
   public function numberWiseLoadHistory(Request $request)
{
    $users = User::where('role', 5)->get();
    
    $query = LoadCampaign30day::with(['trx_user', 'package_info'])
        ->select([
            'targeted_number',
            DB::raw('MAX(owner_name) as owner_name'),
            DB::raw('MAX(user_id) as user_id'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(campaign_price) as total_amount'),
            DB::raw('MAX(created_at) as last_transaction_date')
        ])
        ->groupBy('targeted_number'); // Group only by phone number

    // User filter
    if ($request->has('user_id') && $request->user_id) {
        $query->where('user_id', $request->user_id);
    }

    // Target number filter - handles 880, 0, and partial numbers
    if ($request->has('target_number') && $request->target_number) {
        $searchNumber = $request->target_number;
        
        // Remove any spaces or special characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $searchNumber);
        
        // If number starts with 880, also search for 0 and without prefix
        if (str_starts_with($cleanNumber, '880')) {
            $without880 = substr($cleanNumber, 3); // Remove 880
            $with0 = '0' . $without880; // Add 0 prefix
            
            $query->where(function($q) use ($cleanNumber, $without880, $with0) {
                $q->where('targeted_number', 'like', '%' . $cleanNumber . '%')
                  ->orWhere('targeted_number', 'like', '%' . $without880 . '%')
                  ->orWhere('targeted_number', 'like', '%' . $with0 . '%');
            });
        }
        // If number starts with 0, also search for 880 and without prefix
        elseif (str_starts_with($cleanNumber, '0')) {
            $without0 = substr($cleanNumber, 1); // Remove 0
            $with880 = '880' . $without0; // Add 880 prefix
            
            $query->where(function($q) use ($cleanNumber, $without0, $with880) {
                $q->where('targeted_number', 'like', '%' . $cleanNumber . '%')
                  ->orWhere('targeted_number', 'like', '%' . $without0 . '%')
                  ->orWhere('targeted_number', 'like', '%' . $with880 . '%');
            });
        }
        // If number doesn't start with 880 or 0, search all formats
        else {
            $with0 = '0' . $cleanNumber; // Add 0 prefix
            $with880 = '880' . $cleanNumber; // Add 880 prefix
            
            $query->where(function($q) use ($cleanNumber, $with0, $with880) {
                $q->where('targeted_number', 'like', '%' . $cleanNumber . '%')
                  ->orWhere('targeted_number', 'like', '%' . $with0 . '%')
                  ->orWhere('targeted_number', 'like', '%' . $with880 . '%');
            });
        }
    }

    $numberWiseHistory = $query->get();

    return view('admin.flexiload.number-wise-history', [
        'numberWiseHistory' => $numberWiseHistory,
        'users' => $users
    ]);
}

    /**
     * Detailed Transaction History for a Specific User
     */
   public function userDetailedHistory($userId)
{
    $user = User::with('userDetail')->findOrFail($userId);
    
    $transactions = LoadCampaign30day::with(['package_info'])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    $summary = LoadCampaign30day::where('user_id', $userId)
        ->select([
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(campaign_price) as total_amount'),
            DB::raw('COUNT(DISTINCT targeted_number) as unique_numbers')
        ])
        ->first();

    // Collect transaction IDs from LoadCampaign30day
    $transactionIds = [];
    foreach ($transactions as $transaction) {
        if (!empty($transaction->transaction_id)) {
            $transactionIds[] = $transaction->transaction_id;
        }
    }

    // Fetch all SMS messages for this user's transactions
    $allMessages = LoadSimMessages::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

    // Map messages to transactions using extracted transaction ID
    $transactionMessages = [];
    foreach ($allMessages as $msg) {
        $trxId = LoadSimMessages::getTransactionIdFromMessage($msg->message, $msg->operator_company);
        
        if ($trxId && in_array($trxId, $transactionIds)) {
            $transactionMessages[$trxId][] = $msg;
        }
    }

    return view('admin.flexiload.user-detailed-history', [
        'user' => $user,
        'transactions' => $transactions,
        'summary' => $summary,
        'transactionMessages' => $transactionMessages
    ]);
}
    /**
     * Detailed Transaction History for a Specific Number
     */
public function numberDetailedHistory($number)
{
    // Get transactions for this number
    $transactions = LoadCampaign30day::with(['trx_user', 'package_info'])
        ->where('targeted_number', $number)
        ->orderBy('created_at', 'desc')
        ->paginate(20);

    // Summary
    $summary = LoadCampaign30day::where('targeted_number', $number)
        ->select([
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(campaign_price) as total_amount'),
            DB::raw('MAX(created_at) as last_transaction_date')
        ])
        ->first();

    // Collect transaction IDs from LoadCampaign30day
    $transactionIds = [];
    foreach ($transactions as $transaction) {
        if (!empty($transaction->transaction_id)) {
            $transactionIds[] = $transaction->transaction_id;
        }
    }

    // Fetch all SMS messages for this number
    $allMessages = LoadSimMessages::where('sender', 'like', '%' . $number . '%')
        ->orWhere('sim_no', $number)
        ->orderBy('created_at', 'desc')
        ->get();

    // Map messages to transactions using extracted transaction ID
    $transactionMessages = [];
    foreach ($allMessages as $msg) {
        $trxId = LoadSimMessages::getTransactionIdFromMessage($msg->message, $msg->operator_company);
        
        // Debug: Log the extracted transaction ID
        \Log::info('Message Transaction ID Extraction', [
            'message' => $msg->message,
            'extracted_trx_id' => $trxId,
            'transaction_ids' => $transactionIds
        ]);

        if ($trxId && in_array($trxId, $transactionIds)) {
            $transactionMessages[$trxId][] = $msg;
        }
    }

    return view('admin.flexiload.number-detailed-history', [
        'phoneNumber' => $number,
        'transactions' => $transactions,
        'summary' => $summary,
        'transactionMessages' => $transactionMessages
    ]);
}

    /**
     * Get Load History Summary (Dashboard)
     */
    public function loadHistorySummary(Request $request)
    {
        $users = User::where('role', 5)->get();
        
        $query = LoadCampaign30day::query();

        // User filter
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Date filter
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $summary = $query->select([
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(campaign_price) as total_amount'),
            DB::raw('COUNT(DISTINCT user_id) as total_users'),
            DB::raw('COUNT(DISTINCT targeted_number) as total_numbers')
        ])->first();

        // Recent transactions
        $recentTransactions = $query->with(['trx_user', 'package_info'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.flexiload.history-summary', [
            'summary' => $summary,
            'recentTransactions' => $recentTransactions,
            'users' => $users
        ]);
    }
    
}

