<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Model\LoadCampaign30day;
use App\Model\LoadCamPending;
use App\Model\LoadSimMessages;
use App\Model\LoadSimAvailablleBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IRechargeWebhookController extends Controller
{
    /**
     * Handle iRecharge webhook callback
     */
    public function handle(Request $request)
    {
        // Mark that webhook was hit (for debugging)
        Cache::put('irecharge_last_webhook_hit', now(), 3600);
        
        // Log EVERYTHING that comes in
        Log::channel('webhook')->info('IRECHARGE WEBHOOK RECEIVED', [
            'timestamp' => now()->toDateTimeString(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'full_url' => $request->fullUrl(),
            'all_input' => $request->all(),
            'raw_content' => $request->getContent()
        ]);

        try {
            // Get payload from request
            $raw = $request->getContent();
            $payload = json_decode($raw, true);

            if (!is_array($payload)) {
                $payload = $request->all();
            }

            Log::channel('webhook')->info('Parsed payload', ['payload' => $payload]);

            // Extract data from iRecharge webhook
            $transactionId = $payload['transaction_id'] ?? null;
            $reference = $payload['reference'] ?? null;
            $status = strtolower($payload['status'] ?? 'unknown');
            $amount = $payload['amount'] ?? null;
            $recipientNumber = $payload['recipient_number'] ?? null;
            $gatewayReference = $payload['gateway_reference'] ?? null;
            $statusMessage = $payload['status_message'] ?? '';
            
            // Get response payload if exists
            $responsePayload = $payload['response_payload'] ?? [];
            $rawOutput = $responsePayload['raw_output'] ?? $statusMessage;

            Log::channel('webhook')->info('Extracted webhook data', [
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'status' => $status,
                'amount' => $amount,
                'recipient_number' => $recipientNumber,
                'gateway_reference' => $gatewayReference,
                'raw_output' => substr($rawOutput, 0, 200)
            ]);

            // Validate required fields
            if (!$reference) {
                Log::channel('webhook')->error('Missing reference in webhook', [
                    'payload' => $payload
                ]);
                return response()->json(['success' => false, 'error' => 'Missing reference'], 200);
            }

            // Find pending transaction
            $pending = LoadCamPending::where('sms_id', $reference)->first();

            if (!$pending) {
                Log::channel('webhook')->warning('Pending transaction not found', [
                    'reference' => $reference,
                    'searched_sms_id' => $reference
                ]);
                return response()->json(['success' => true, 'message' => 'Transaction not found'], 200);
            }

            Log::channel('webhook')->info('Found pending transaction', [
                'pending_id' => $pending->id,
                'current_status' => $pending->status,
                'sms_id' => $pending->sms_id,
                'operator' => $pending->operator_id,
                'amount' => $pending->campaign_price
            ]);

            // Check if already processed
            $alreadyProcessed = LoadCampaign30day::where('sms_id', $reference)->exists();
            if ($alreadyProcessed) {
                Log::channel('webhook')->warning('Duplicate webhook ignored', [
                    'reference' => $reference,
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['success' => true, 'message' => 'Already processed'], 200);
            }

            // Process based on status
            if (in_array($status, ['success', 'successful', 'completed', 'approve'])) {
                Log::channel('webhook')->info('Processing SUCCESS status');
                $this->processSuccess($pending, $payload, $rawOutput, $gatewayReference);
            } 
            elseif (in_array($status, ['failed', 'error', 'rejected', 'decline'])) {
                Log::channel('webhook')->info('Processing FAILURE status');
                $this->processFailure($pending, $payload, $rawOutput);
            } 
            elseif (in_array($status, ['waiting', 'pending', 'processing'])) {
                Log::channel('webhook')->info('Processing WAITING status');
                $this->processWaiting($pending, $payload, $transactionId);
            } 
            else {
                Log::channel('webhook')->warning('Unknown status received', [
                    'status' => $status,
                    'full_payload' => $payload
                ]);
                // Still update pending with the unknown status
                $pending->update([
                    'status' => 2,
                    'remarks' => 'pending' // Simple remarks
                ]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Throwable $e) {
            Log::channel('webhook')->error('WEBHOOK PROCESSING ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * Process successful transaction
     */
    private function processSuccess($pending, $payload, $rawOutput, $gatewayReference)
    {
        DB::beginTransaction();

        try {
            // Get transaction details
            $amount = $payload['amount'] ?? $pending->campaign_price;
            $recipientNumber = $payload['recipient_number'] ?? $pending->targeted_number;
            $transactionId = $gatewayReference ?? $payload['transaction_id'] ?? $payload['gateway_reference'];
            $statusMessage = $payload['status_message'] ?? 'Recharge Successful';
            
            // If no gateway reference, try to extract from raw output
            if (!$transactionId && $rawOutput) {
                if (preg_match('/transaction ID\s+([A-Z0-9\.]+)/i', $rawOutput, $matches)) {
                    $transactionId = $matches[1];
                } elseif (preg_match('/ID\s+([A-Z0-9\.]+)/i', $rawOutput, $matches)) {
                    $transactionId = $matches[1];
                }
            }
            
            // Extract SIM balance from response
            $simBalance = $this->extractSimBalance($rawOutput);
            
            // Format display number
            $displayNumber = preg_replace('/^88/', '', $recipientNumber);
            $displayNumber = ltrim($displayNumber, '0');
            
            // Create success message (short version)
            $messageText = "Recharge Request of TK {$amount} for mobile no {$displayNumber}, transaction ID {$transactionId} is successful.";
            if ($simBalance) {
                $messageText .= " Your account balance is TK {$simBalance}.";
            }
            
            Log::channel('webhook')->info('Creating success records', [
                'sms_id' => $pending->sms_id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'balance' => $simBalance
            ]);
            
            // Create LoadSimMessages record
            LoadSimMessages::create([
                'user_id' => $pending->user_id,
                'sim_no' => $pending->sim_number ?? $recipientNumber,
                'operator_company' => $pending->operator_id,
                'message' => $messageText,
                'sender' => 'iRecharge',
                'serial_id' => $pending->sms_id,
                'status' => 1
            ]);
            
            // Create LoadCampaign30day record with SIMPLE remarks (just "success")
            LoadCampaign30day::create([
                'user_id' => $pending->user_id,
                'operator_id' => $pending->operator_id,
                'sms_id' => $pending->sms_id,
                'campaign_id' => $pending->campaign_id,
                'targeted_number' => $recipientNumber,
                'owner_name' => $pending->owner_name,
                'package_id' => $pending->package_id,
                'number_type' => $pending->number_type,
                'campaign_type' => $pending->campaign_type,
                'campaign_price' => $amount,
                'api_port' => $pending->api_port,
                'transaction_id' => $transactionId ?? $pending->sms_id,
                'remarks' => 'success', // SIMPLE: just "success"
                'status' => 1
            ]);
            
            // Update SIM balance if extracted
            if ($simBalance) {
                $this->updateSimBalance($pending->operator_id, $simBalance);
            }
            
            // Delete pending record
            $pendingId = $pending->id;
            $pending->delete();
            
            DB::commit();
            
            Log::channel('webhook')->info('Successfully processed transaction', [
                'pending_id' => $pendingId,
                'sms_id' => $pending->sms_id,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'remarks' => 'success'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('webhook')->error('SUCCESS PROCESSING ERROR', [
                'sms_id' => $pending->sms_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Try to update pending with error
            try {
                $pending->update([
                    'status' => 3,
                    'remarks' => 'pending' // SIMPLE: just "pending"
                ]);
            } catch (\Exception $updateError) {
                Log::channel('webhook')->error('Could not update pending record', [
                    'error' => $updateError->getMessage()
                ]);
            }
        }
    }

    /**
     * Process failed transaction
     */
    private function processFailure($pending, $payload, $rawOutput)
    {
        DB::beginTransaction();

        try {
            $statusMessage = $payload['status_message'] ?? 'Recharge Failed';
            $errorDetails = $payload['error'] ?? $payload['message'] ?? $statusMessage;
            
            Log::channel('webhook')->info('Processing failed transaction', [
                'sms_id' => $pending->sms_id,
                'reason' => $errorDetails
            ]);
            
            // Create failed record in LoadCampaign30day with SIMPLE remarks (just "failed")
            LoadCampaign30day::create([
                'user_id' => $pending->user_id,
                'operator_id' => $pending->operator_id,
                'sms_id' => $pending->sms_id,
                'campaign_id' => $pending->campaign_id,
                'targeted_number' => $pending->targeted_number,
                'owner_name' => $pending->owner_name,
                'package_id' => $pending->package_id,
                'number_type' => $pending->number_type,
                'campaign_type' => $pending->campaign_type,
                'campaign_price' => $pending->campaign_price,
                'api_port' => $pending->api_port,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'remarks' => 'failed', // SIMPLE: just "failed"
                'status' => 0
            ]);
            
            // Create failure message in LoadSimMessages
            LoadSimMessages::create([
                'user_id' => $pending->user_id,
                'sim_no' => $pending->sim_number ?? $pending->targeted_number,
                'operator_company' => $pending->operator_id,
                'message' => "Recharge failed for {$pending->targeted_number}. Amount: TK {$pending->campaign_price}. Reason: {$errorDetails}",
                'sender' => 'iRecharge',
                'serial_id' => $pending->sms_id,
                'status' => 0
            ]);
            
            // Delete pending record
            $pendingId = $pending->id;
            $pending->delete();
            
            DB::commit();
            
            Log::channel('webhook')->info('Successfully processed failure', [
                'pending_id' => $pendingId,
                'sms_id' => $pending->sms_id,
                'reason' => $errorDetails,
                'remarks' => 'failed'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('webhook')->error('FAILURE PROCESSING ERROR', [
                'sms_id' => $pending->sms_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process waiting/pending transaction
     */
    private function processWaiting($pending, $payload, $transactionId)
    {
        DB::beginTransaction();

        try {
            $statusMessage = $payload['status_message'] ?? 'Processing transaction';
            
            Log::channel('webhook')->info('Updating transaction to waiting status', [
                'sms_id' => $pending->sms_id,
                'transaction_id' => $transactionId,
                'message' => $statusMessage
            ]);
            
            // Update pending record with waiting status - SIMPLE remarks (just "pending")
            $pending->status = 2; // 2 = waiting/processing
            $pending->gateway_transaction_id = $transactionId;
            $pending->transaction_id = $transactionId;
            $pending->remarks = 'pending'; // SIMPLE: just "pending"
            $pending->save();
            
            DB::commit();
            
            Log::channel('webhook')->info('Transaction status updated to waiting', [
                'pending_id' => $pending->id,
                'sms_id' => $pending->sms_id,
                'transaction_id' => $transactionId,
                'remarks' => 'pending'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('webhook')->error('WAITING PROCESSING ERROR', [
                'sms_id' => $pending->sms_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Extract SIM balance from response message
     */
    private function extractSimBalance($message)
    {
        if (!$message) return null;
        
        // Pattern 1: "balance is TK 2975.07"
        if (preg_match('/balance.*?TK\s*([0-9,]+\.?[0-9]*)/i', $message, $matches)) {
            return (float) str_replace(',', '', $matches[1]);
        }
        
        // Pattern 2: "Your account balance is TK 2935.07"
        if (preg_match('/account balance.*?TK\s*([0-9,]+\.?[0-9]*)/i', $message, $matches)) {
            return (float) str_replace(',', '', $matches[1]);
        }
        
        // Pattern 3: "balance TK 2975.07"
        if (preg_match('/balance\s+TK\s*([0-9,]+\.?[0-9]*)/i', $message, $matches)) {
            return (float) str_replace(',', '', $matches[1]);
        }
        
        return null;
    }

    /**
     * Update SIM balance in database
     */
    private function updateSimBalance($operator, $balance)
    {
        try {
            $map = [
                'airtel' => 'airtel',
                'gp' => 'gp',
                'grameenphone' => 'gp',
                'blink' => 'blink',
                'banglalink' => 'blink',
                'robi' => 'robi',
                'teletalk' => 'teletalk',
            ];

            $column = $map[$operator] ?? null;

            if (!$column) {
                Log::channel('webhook')->warning('Unknown operator for balance update', ['operator' => $operator]);
                return;
            }

            $sim = LoadSimAvailablleBalance::first();
            
            if (!$sim) {
                $sim = new LoadSimAvailablleBalance();
            }
            
            $sim->$column = $balance;
            $sim->status = 1;
            $sim->save();
            
            Log::channel('webhook')->info('SIM balance updated', [
                'operator' => $operator,
                'column' => $column,
                'balance' => $balance
            ]);
            
        } catch (\Exception $e) {
            Log::channel('webhook')->error('Failed to update SIM balance', [
                'operator' => $operator,
                'balance' => $balance,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        $lastWebhookHit = Cache::get('irecharge_last_webhook_hit');
        
        return response()->json([
            'status' => 'ok',
            'service' => 'iRecharge Webhook',
            'timestamp' => now(),
            'webhook_url' => route('webhook.irecharge'),
            'last_webhook_hit' => $lastWebhookHit,
            'last_hit_ago' => $lastWebhookHit ? $lastWebhookHit->diffForHumans() : 'Never',
            'environment' => app()->environment(),
            'laravel_version' => app()->version()
        ]);
    }

    /**
     * Get webhook status (protected endpoint)
     */
    public function status(Request $request)
    {
        // This endpoint is protected by auth:api middleware
        $recentPending = LoadCamPending::where('status', 2)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
        
        $recentCompleted = LoadCampaign30day::orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return response()->json([
            'webhook_url' => route('webhook.irecharge'),
            'last_webhook_hit' => Cache::get('irecharge_last_webhook_hit'),
            'pending_waiting_count' => LoadCamPending::where('status', 2)->count(),
            'total_pending_count' => LoadCamPending::count(),
            'recent_waiting_transactions' => $recentPending,
            'recent_completed_transactions' => $recentCompleted,
            'server_time' => now()
        ]);
    }

    /**
     * Test endpoint for debugging
     */
    public function test(Request $request)
    {
        Log::channel('webhook')->info('TEST WEBHOOK HIT', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
            'content' => $request->getContent(),
            'ip' => $request->ip()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Webhook test endpoint working',
            'received_data' => $request->all(),
            'timestamp' => now(),
            'webhook_config' => [
                'url' => route('webhook.irecharge'),
                'method' => 'POST',
                'expected_format' => 'JSON'
            ]
        ]);
    }
}
