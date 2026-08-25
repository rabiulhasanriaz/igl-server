<?php

namespace App\Http\Controllers\Admin;

use App\Model\Operator;
use App\Model\SenderIdVirtualNumber;
use App\Model\SenderIdRegister;
use App\Model\SmsCamPending;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class VirtualNumberController extends Controller
{
    /*list of all virtual numbers*/
    public function index()
    {
        $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
        return view('admin.virtualNumber.virtual_number_list', compact('virtualNumbers'));
    }

    /*show form of create new virtual number*/
    public function create()
    {
        $operators = Operator::take(9)->get();
        return view('admin.virtualNumber.add_virtual_number', compact('operators'));
    }

    /*store new virtual number*/
    public function store(Request $request)
    {
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'operator_id' => ['required', 'numeric'],
            'virtual_number' => ['required'],
            'api_username' => ['required'],
            'api_password' => ['required'],
            'auto_load_amount' => ['required'],
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withInput()->withErrors($validateData);
        }

        $createVirtualNumber = SenderIdVirtualNumber::create([
            'operator_id' => $request->operator_id,
            'sivn_number' => $request->virtual_number,
            'sivn_name' => $request->virtual_number_name,
            'sivn_api_user_name' => $request->api_username,
            'sivn_api_password' => $request->api_password,
            'sivn_load_amount' => $request->auto_load_amount,
        ]);
        
        if ($createVirtualNumber == true) {
            session()->flash('type', 'success');
            session()->flash('message', 'virtual number added successfully...... ');
            return redirect()->route('admin.virtualNumber.index');
        } else {
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to add virtual number. please try again........');
            return redirect()->back()->withInput();
        }
    }

    /*show edit form of virtual number*/
    public function edit($id)
    {
        try {
            $operators = Operator::get();
            $virtualNumber = SenderIdVirtualNumber::where('id', $id)->first();
            if($virtualNumber) {
                return view('admin.virtualNumber.edit_virtual_number', compact('operators', 'virtualNumber'));
            }
            else{
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find virtual number. please try again........!');
                $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
                return redirect()->route('admin.virtualNumber.index', compact('virtualNumbers'));
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'can\'t find virtual number. please try again.........!');
            $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
            return redirect()->route('admin.virtualNumber.index', compact('virtualNumbers'));
        }
    }

    /*update virtual number*/
    public function update(Request $request, $id)
    {
        /*validate input data*/
        $validateData = Validator::make($request->all(), [
            'operator_id' => ['required', 'numeric'],
            'virtual_number' => ['required'],
            'api_username' => ['required'],
            'api_password' => ['required'],
            'auto_load_amount' => ['required']
        ]);

        if ($validateData->fails()) {
            return redirect()->back()->withErrors($validateData);
        }

        try {
            $updSenderId = SenderIdVirtualNumber::where('id', $id)->first();
            if ($updSenderId) {
                $updSenderId->operator_id = $request->operator_id;
                $updSenderId->sivn_number = $request->virtual_number;
                $updSenderId->sivn_name = $request->virtual_number_name;
                $updSenderId->sivn_api_user_name = $request->api_username;
                $updSenderId->sivn_api_password = $request->api_password;
                $updSenderId->sivn_load_amount = $request->auto_load_amount;
                $updSenderId->save();

                session()->flash('type', 'success');
                session()->flash('message', 'Virtual Number updated successfully......!');
                $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
                return redirect()->route('admin.virtualNumber.index', compact('virtualNumbers'));
            } else {
                session()->flash('type', 'danger');
                session()->flash('message', 'can\'t find this virtual number. please try again.....!');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to edit virtual number.....!');
            return redirect()->back();
        }
    }

    /*delete virtual number*/
    public function delete($id)
    {
        try{
            SenderIdVirtualNumber::where('id', $id)->delete();
            session()->flash('type', 'success');
            session()->flash('message', 'Virtual Number deleted successfully......!');
            return redirect()->back();
        }
        catch (\Exception $e){
            session()->flash('type', 'danger');
            session()->flash('message', 'something went wrong to delete virtual number.....!');
            return redirect()->back();
        }
    }

    /**
     * Check balance for a specific virtual number
     */
public function balanceCheck($id)
{
    try {

        $virtualNumber = SenderIdVirtualNumber::with('operator')
            ->where('id', $id)
            ->first();

        if (!$virtualNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Virtual number not found'
            ]);
        }

        // check refresh from request
        $refresh = request()->get('refresh', 0);

        $cacheKey = 'balance_' . $virtualNumber->id;

        // if refresh = 1 → ignore cache
        $cachedBalance = null;

        if (!$refresh) {
            $cachedBalance = Cache::get($cacheKey);
        }

        // =========================
        // RETURN CACHE IF AVAILABLE
        // =========================
        if ($cachedBalance) {

            return response()->json([
                'success' => true,
                'data' => $cachedBalance,
                'cached' => true,
                'virtual_name' => $virtualNumber->sivn_name,
                'virtual_number' => $virtualNumber->sivn_number,
                'operator' => $virtualNumber->operator->ope_operator_name ?? 'Unknown',
                'username' => $virtualNumber->sivn_api_user_name
            ]);
        }

        // =========================
        // FETCH FRESH BALANCE
        // =========================
        $balance = $this->getBalanceByOperator($virtualNumber);

        // =========================
        // CACHE ONLY SUCCESS DATA
        // =========================
        if (!empty($balance) && (isset($balance['success']) && $balance['success'])) {
            Cache::put($cacheKey, $balance, now()->addMinutes(5));
        }

        return response()->json([
            'success' => true,
            'data' => $balance,
            'cached' => false,
            'virtual_name' => $virtualNumber->sivn_name,
            'virtual_number' => $virtualNumber->sivn_number,
            'operator' => $virtualNumber->operator->ope_operator_name ?? 'Unknown',
            'username' => $virtualNumber->sivn_api_user_name
        ]);

    } catch (\Exception $e) {

        Log::error('Balance Check Error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Error checking balance: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Show low balance virtual numbers
     */
    public function lowBalance()
    {
        $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
        $lowBalanceNumbers = [];
        $balanceData = [];
        
        foreach ($virtualNumbers as $vn) {
            $operatorName = $vn->operator->ope_operator_name ?? '';
            
            $apiCodes = [
                'GP' => 'GP', 'Robi' => 'RB', 'Airtel' => 'RB',
                'Banglalink' => 'BL', 'Teletalk' => 'TT',
                'RangsTel' => 'RT', 'BanglarPhone' => 'BN',
                'IGL Tel' => 'ADN', 'Premium' => 'PRM', 'AmberIT' => 'AIT',
                'FusionNet' => 'FN', 'Brilliant' => 'BR', 'Metronet' => 'MTN',
                'RaceOnline' => 'RCO', 'Mirnet' => 'MN', 'Bracnet' => 'BN',
            ];
            
            $code = $apiCodes[$operatorName] ?? null;
            
            if ($code) {
                $balance = $this->getAnsBalance($code, $vn->sivn_api_user_name, $vn->sivn_api_password);
                
                if ($balance['success']) {
                    $availableBalance = $this->extractBalance($balance);
                    
                    if ($availableBalance !== null) {
                        $balanceData[$vn->id] = [
                            'id' => $vn->id,
                            'virtual_number' => $vn->sivn_number,
                            'virtual_name' => $vn->sivn_name,
                            'operator' => $operatorName,
                            'username' => $vn->sivn_api_user_name,
                            'balance' => $availableBalance,
                            'load_amount' => $vn->sivn_load_amount,
                            'created_at' => $vn->created_at,
                            'status' => $availableBalance < 500 ? 'Low Balance' : ($availableBalance < 1000 ? 'Warning' : 'Good'),
                            'status_class' => $availableBalance < 500 ? 'danger' : ($availableBalance < 1000 ? 'warning' : 'success')
                        ];
                        
                        // Check if balance is low (less than 500 TK) or warning (less than 1000)
                        if ($availableBalance < 1000) {
                            $lowBalanceNumbers[] = $balanceData[$vn->id];
                        }
                    }
                }
            }
        }
        
        // Sort by balance (lowest first)
        usort($lowBalanceNumbers, function($a, $b) {
            return $a['balance'] - $b['balance'];
        });
        
        // Calculate summary
        $summary = [
            'total_low_balance' => count(array_filter($lowBalanceNumbers, function($item) { return $item['balance'] < 500; })),
            'total_warning' => count(array_filter($lowBalanceNumbers, function($item) { return $item['balance'] >= 500 && $item['balance'] < 1000; })),
            'lowest_balance' => !empty($lowBalanceNumbers) ? min(array_column($lowBalanceNumbers, 'balance')) : 0,
            'total_numbers' => count($lowBalanceNumbers)
        ];
        
        return view('admin.virtualNumber.low_balance_list', compact('lowBalanceNumbers', 'summary'));
    }

    /**
     * Extract balance from API response
     */
    private function extractBalance($balanceResponse)
    {
        if (isset($balanceResponse['data']['data']['availableBalance'])) {
            return floatval($balanceResponse['data']['data']['availableBalance']);
        } elseif (isset($balanceResponse['data']['availableBalance'])) {
            return floatval($balanceResponse['data']['availableBalance']);
        } elseif (isset($balanceResponse['data']['data']['balance'])) {
            return floatval($balanceResponse['data']['data']['balance']);
        } elseif (isset($balanceResponse['data']['balance'])) {
            return floatval($balanceResponse['data']['balance']);
        } elseif (isset($balanceResponse['data']['response']['availableBalance'])) {
            return floatval($balanceResponse['data']['response']['availableBalance']);
        } elseif (isset($balanceResponse['data']['credit'])) {
            return floatval($balanceResponse['data']['credit']);
        }
        return null;
    }

    /**
     * Get balance based on operator type
     */
    private function getBalanceByOperator($virtualNumber)
    {
        $operatorName = $virtualNumber->operator->ope_operator_name ?? '';
        
        $apiCodes = [
            'GP' => 'GP', 'Robi' => 'RB', 'Airtel' => 'RB',
            'Banglalink' => 'BL', 'Teletalk' => 'TT',
            'RangsTel' => 'RT', 'BanglarPhone' => 'BN',
            'IGL Tel' => 'ADN', 'Premium' => 'PRM', 'AmberIT' => 'AIT',
            'FusionNet' => 'FN', 'Brilliant' => 'BR', 'Metronet' => 'MTN',
            'RaceOnline' => 'RCO', 'Mirnet' => 'MN', 'Bracnet' => 'BN',
        ];
        
        $code = $apiCodes[$operatorName] ?? null;
        
        if (!$code) {
            return [
                'success' => false,
                'message' => 'Unknown operator: ' . $operatorName
            ];
        }
        
        return $this->getAnsBalance($code, $virtualNumber->sivn_api_user_name, $virtualNumber->sivn_api_password);
    }

    /**
     * Get balance from ANS API
     */
    private function getAnsBalance($code, $username, $password)
    {
        $cacheKey = 'balance_api_' . $username;
        $cachedBalance = Cache::get($cacheKey);
        
        if ($cachedBalance) {
            return $cachedBalance;
        }
        
        $url = "https://api.mnpspbd.com/a2p-proxy-api/api/v1/check-credit-balance";

        $payload = [
            "username" => $username,
            "password" => $password,
            "mno"      => $code,
            "apiKey"   => "myQ1uzu3mRVWdjVq4A1mV5GscebslZ4y",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'message' => 'CURL Error: ' . $error
            ];
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        
        $result = [
            'success' => true,
            'data' => $decodedResponse,
            'operator_code' => $code,
            'username' => $username
        ];
        
        Cache::put($cacheKey, $result, 300);
        
        return $result;
    }

    /**
     * Refresh low balance list (AJAX)
     */
    public function refreshLowBalance()
    {
        // Clear all balance caches
        $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
        foreach ($virtualNumbers as $vn) {
            Cache::forget('balance_api_' . $vn->sivn_api_user_name);
        }
        
        // Get fresh low balance data
        $lowBalanceNumbers = [];
        
        foreach ($virtualNumbers as $vn) {
            $operatorName = $vn->operator->ope_operator_name ?? '';
            
            $apiCodes = [
                'GP' => 'GP', 'Robi' => 'RB', 'Airtel' => 'RB',
                'Banglalink' => 'BL', 'Teletalk' => 'TT',
                'RangsTel' => 'RT', 'BanglarPhone' => 'BN',
                'IGL Tel' => 'ADN', 'Premium' => 'PRM', 'AmberIT' => 'AIT',
                'FusionNet' => 'FN', 'Brilliant' => 'BR', 'Metronet' => 'MTN',
                'RaceOnline' => 'RCO', 'Mirnet' => 'MN', 'Bracnet' => 'BN',
            ];
            
            $code = $apiCodes[$operatorName] ?? null;
            
            if ($code) {
                $balance = $this->getAnsBalance($code, $vn->sivn_api_user_name, $vn->sivn_api_password);
                
                if ($balance['success']) {
                    $availableBalance = $this->extractBalance($balance);
                    
                    if ($availableBalance !== null && $availableBalance < 1000) {
                        $lowBalanceNumbers[] = [
                            'id' => $vn->id,
                            'virtual_number' => $vn->sivn_number,
                            'virtual_name' => $vn->sivn_name,
                            'operator' => $operatorName,
                            'username' => $vn->sivn_api_user_name,
                            'balance' => $availableBalance,
                            'load_amount' => $vn->sivn_load_amount,
                            'status' => $availableBalance < 500 ? 'Low Balance' : 'Warning',
                            'status_class' => $availableBalance < 500 ? 'danger' : 'warning'
                        ];
                    }
                }
            }
        }
        
        usort($lowBalanceNumbers, function($a, $b) {
            return $a['balance'] - $b['balance'];
        });
        
        return response()->json([
            'success' => true,
            'data' => $lowBalanceNumbers,
            'message' => 'Low balance list refreshed successfully'
        ]);
    }

    /**
     * Bulk balance check for all virtual numbers
     */
    public function bulkBalanceCheck()
    {
        $virtualNumbers = SenderIdVirtualNumber::with('operator')->get();
        $balances = [];
        
        foreach ($virtualNumbers as $vn) {
            $operatorName = $vn->operator->ope_operator_name ?? 'Unknown';
            
            $apiCodes = [
                'GP' => 'GP', 'Robi' => 'RB', 'Airtel' => 'RB',
                'Banglalink' => 'BL', 'Teletalk' => 'TT',
                'RangsTel' => 'RT', 'BanglarPhone' => 'BN',
                'IGL Tel' => 'ADN', 'Premium' => 'PRM', 'AmberIT' => 'AIT',
                'FusionNet' => 'FN', 'Brilliant' => 'BR', 'Metronet' => 'MTN',
                'RaceOnline' => 'RCO', 'Mirnet' => 'MN', 'Bracnet' => 'BN',
            ];
            
            $code = $apiCodes[$operatorName] ?? null;
            
            if ($code) {
                $balance = $this->getAnsBalance($code, $vn->sivn_api_user_name, $vn->sivn_api_password);
                $balances[$vn->id] = [
                    'number' => $vn->sivn_number,
                    'name' => $vn->sivn_name,
                    'operator' => $operatorName,
                    'balance' => $balance
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'balances' => $balances,
            'checked_at' => Carbon::now()->toDateTimeString()
        ]);
    }
    /**
 * Get sender IDs for a specific operator
 */
public function getSenderIds(Request $request)
{
    $virtualNumberId = $request->get('virtual_number_id');
    
    // Get only IPTSP numbers from sender_id_registers
    $iptspNumbers = SenderIdRegister::where('sir_status', 1)
        ->where(function($query) {
            $query->where('sir_sender_id', 'LIKE', '88096%')
                  ->orWhere('sir_sender_id', 'LIKE', '88044%')
                  ->orWhere('sir_sender_id', 'LIKE', '880961%')
                  ->orWhere('sir_sender_id', 'LIKE', '880962%')
                  ->orWhere('sir_sender_id', 'LIKE', '880963%')
                  ->orWhere('sir_sender_id', 'LIKE', '880964%')
                  ->orWhere('sir_sender_id', 'LIKE', '880965%')
                  ->orWhere('sir_sender_id', 'LIKE', '880966%')
                  ->orWhere('sir_sender_id', 'LIKE', '880967%')
                  ->orWhere('sir_sender_id', 'LIKE', '880968%')
                  ->orWhere('sir_sender_id', 'LIKE', '880969%');
        })
        ->get(['id', 'sir_sender_id']);
    
    // Find all SenderIdRegister entries that use this virtual number
    $senderRegisters = SenderIdRegister::where('sir_gp_vn', $virtualNumberId)
        ->orWhere('sir_robi_vn', $virtualNumberId)
        ->orWhere('sir_airtel_vn', $virtualNumberId)
        ->orWhere('sir_banglalink_vn', $virtualNumberId)
        ->orWhere('sir_teletalk_vn', $virtualNumberId)
        ->pluck('id');
    
    // Count pending messages
    $pendingCount = SmsCamPending::whereIn('sender_id', $senderRegisters)
        ->where('scp_campaign_status', 1)
        ->count();
    
    return response()->json([
        'success' => true,
        'senders' => $iptspNumbers,
        'pending_count' => $pendingCount,
        'virtual_number_id' => $virtualNumberId,
        'affected_sender_registers' => $senderRegisters
    ]);
}
/**
 * Change sender ID for pending messages
 */
/**
 * Change sender ID for pending messages
 */
public function changeSenderForPending(Request $request)
{
    try {
        $virtualNumberId = $request->get('virtual_number_id'); // ID from sender_id_virtual_numbers table
        $newSenderId = $request->get('new_sender_id'); // New IPTSP sender ID from sender_id_registers table
        
        // Verify new sender exists and is IPTSP
        $newSender = SenderIdRegister::find($newSenderId);
        if (!$newSender) {
            return response()->json([
                'success' => false,
                'message' => 'IPTSP sender not found'
            ]);
        }
        
        // Check if new sender is IPTSP
        $isIptsp = preg_match('/^(88096|88044|880961|880962|880963|880964|880965|880966|880967|880968|880969)/', $newSender->sir_sender_id);
        if (!$isIptsp) {
            return response()->json([
                'success' => false,
                'message' => 'Selected sender is not an IPTSP number'
            ]);
        }
        
        // Find all SenderIdRegister entries that use this virtual number
        $senderRegisters = SenderIdRegister::where('sir_gp_vn', $virtualNumberId)
            ->orWhere('sir_robi_vn', $virtualNumberId)
            ->orWhere('sir_airtel_vn', $virtualNumberId)
            ->orWhere('sir_banglalink_vn', $virtualNumberId)
            ->orWhere('sir_teletalk_vn', $virtualNumberId)
            ->pluck('id');
        
        // Update all pending messages that use any of these SenderIdRegister IDs
        // Also update scp_sms_type to 1 (Non-Masking)
        $updated = SmsCamPending::whereIn('sender_id', $senderRegisters)
            ->where('scp_campaign_status', 1)
            ->update([
                'sender_id' => $newSenderId,
                'scp_sms_type' => 1  // 1 = Non-Masking
            ]);
        
        return response()->json([
            'success' => true,
            'updated_count' => $updated,
            'old_sender_ids' => $senderRegisters,
            'new_sender_id' => $newSenderId,
            'new_sender_number' => $newSender->sir_sender_id,
            'sms_type_updated' => 'Changed to Non-Masking (1)',
            'message' => "Successfully changed {$updated} pending messages to IPTSP number: " . $newSender->sir_sender_id . " and set SMS type to Non-Masking"
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
public function getPendingCount(Request $request)
{
    $virtualNumberId = $request->get('virtual_number_id');
    
    // Find all SenderIdRegister entries that use this virtual number
    $senderRegisters = SenderIdRegister::where('sir_gp_vn', $virtualNumberId)
        ->orWhere('sir_robi_vn', $virtualNumberId)
        ->orWhere('sir_airtel_vn', $virtualNumberId)
        ->orWhere('sir_banglalink_vn', $virtualNumberId)
        ->orWhere('sir_teletalk_vn', $virtualNumberId)
        ->pluck('id');
    
    // Count pending messages where sender_id is in those SenderIdRegister IDs
    $pendingCount = SmsCamPending::whereIn('sender_id', $senderRegisters)
        ->where('scp_campaign_status', 1)
        ->count();
    
    return response()->json([
        'success' => true,
        'pending_count' => $pendingCount,
        'virtual_number_id' => $virtualNumberId,
        'sender_register_ids' => $senderRegisters,
        'message' => "Found {$pendingCount} pending messages for this virtual number"
    ]);
}
}
