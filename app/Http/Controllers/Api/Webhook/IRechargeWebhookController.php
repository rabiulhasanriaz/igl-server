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
        // Mark webhook hit for debugging
        Cache::put('irecharge_last_webhook_hit', now(), 3600);
        
        // Log everything
        Log::channel('webhook')->info('iRecharge Webhook Received', [
            'timestamp' => now()->toDateTimeString(),
            'payload' => $request->all(),
            'raw' => $request->getContent()
        ]);

        try {
            $payload = $request->all();
            
            $reference = $payload['reference'] ?? null;
            $status = strtolower($payload['status'] ?? 'unknown');
            $transactionId = $payload['transaction_id'] ?? null;
            $gatewayReference = $payload['gateway_reference'] ?? $transactionId;
            $amount = $payload['amount'] ?? null;
            $recipientNumber = $payload['recipient_number'] ?? null;
            $statusMessage = $payload['status_message'] ?? '';
            $completedAt = $payload['completed_at'] ?? null;

            Log::channel('webhook')->info('Extracted Data', [
                'reference' => $reference,
                'status' => $status,
                'transaction_id' => $transactionId,
                'gateway_reference' => $gatewayReference,
                'amount' => $amount,
                'recipient_number' => $recipientNumber
            ]);

            // If no reference, try to find pending by transaction_id
            if (!$reference) {
                Log::channel('webhook')->warning('No reference in webhook, trying alternative lookup');
                
                if ($transactionId) {
                    $pending = LoadCamPending::where('gateway_transaction_id', $transactionId)->first();
                    if ($pending) {
                        Log::channel('webhook')->info('Found pending by gateway_transaction_id', [
                            'pending_id' => $pending->id,
                            'sms_id' => $pending->sms_id
                        ]);
                        $reference = $pending->sms_id;
                    }
                }
                
                if (!$pending && $status === 'waiting' && $recipientNumber && $amount) {
                    $cleanNumber = preg_replace('/^88/', '', $recipientNumber);
                    $pending = LoadCamPending::where('targeted_number', 'LIKE', "%{$cleanNumber}%")
                        ->where('campaign_price', $amount)
                        ->where('status', 0)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    if ($pending) {
                        Log::channel('webhook')->info('Found pending by number and amount', [
                            'pending_id' => $pending->id,
                            'sms_id' => $pending->sms_id
                        ]);
                        $reference = $pending->sms_id;
                    }
                }
                
                if (!$pending) {
                    Log::channel('webhook')->error('Cannot find pending record');
                    return response()->json(['success' => true], 200);
                }
            } else {
                $pending = LoadCamPending::where('sms_id', $reference)->first();
            }

            if (!$pending) {
                Log::channel('webhook')->warning('Pending transaction not found', [
                    'reference' => $reference,
                    'transaction_id' => $transactionId
                ]);
                return response()->json(['success' => true], 200);
            }

            Log::channel('webhook')->info('Found pending record', [
                'pending_id' => $pending->id,
                'current_status' => $pending->status,
                'sms_id' => $pending->sms_id
            ]);

            // Update gateway_transaction_id if not set
            if ($transactionId && !$pending->gateway_transaction_id) {
                $pending->gateway_transaction_id = $transactionId;
                $pending->transaction_id = $transactionId;
                $pending->save();
            }

            // Check for duplicate processing
            if (in_array($status, ['success', 'failed'])) {
                $alreadyProcessed = LoadCampaign30day::where('sms_id', $pending->sms_id)->exists();
                if ($alreadyProcessed) {
                    Log::channel('webhook')->warning('Duplicate webhook ignored', [
                        'sms_id' => $pending->sms_id
                    ]);
                    $pending->delete();
                    return response()->json(['success' => true], 200);
                }
            }

            // Process based on status
            if ($status === 'success') {
                $this->processSuccess($pending, $payload, $gatewayReference);
            } elseif ($status === 'failed') {
                $this->processFailure($pending, $payload);
            } elseif ($status === 'waiting') {
                $pending->update([
                    'status' => 2,
                    'remarks' => 'Transaction processing, waiting for confirmation'
                ]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Throwable $e) {
            Log::channel('webhook')->error('Webhook Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false], 200);
        }
    }

    /**
     * Process successful transaction
     */
    /**
 * Process successful transaction
 */
/**
 * Process successful transaction (Works for both GP & Banglalink)
 */
private function processSuccess($pending, $payload, $rawOutput, $gatewayReference)
{
    DB::beginTransaction();

    try {
        // Get basic details
        $amount = $payload['amount'] ?? $pending->campaign_price;
        $recipientNumber = $payload['recipient_number'] ?? $pending->targeted_number;
        
        // --- 1. Intelligently extract REAL Transaction ID and Balance ---
        $realTransactionId = null;
        $simBalance = null;
        
        // Prefer the clean 'gateway_reference' if it exists (Banglalink case)
        if (!empty($gatewayReference)) {
            $realTransactionId = $gatewayReference;
        }
        
        // The full message is usually in 'raw_output'
        $fullMessage = $rawOutput ?: ($payload['status_message'] ?? '');
        
        if ($fullMessage) {
            // If we don't have a transaction ID yet, try to extract it from the message (Grameenphone case)
            if (!$realTransactionId) {
                // Look for "Transaction ID BD100626120831147776"
                if (preg_match('/Transaction ID\s+([A-Z0-9]+)/i', $fullMessage, $matches)) {
                    $realTransactionId = $matches[1];
                }
                // Look for "transaction ID R260609.1244.26004e"
                elseif (preg_match('/transaction ID\s+([A-Z0-9\.]+)/i', $fullMessage, $matches)) {
                    $realTransactionId = $matches[1];
                }
            }
            
            // Extract Balance (Works for both formats)
            // Pattern for "new balance is 33,965.06 BDT"
            if (preg_match('/new balance is\s+([0-9,]+\.?[0-9]*)/i', $fullMessage, $matches)) {
                $simBalance = (float) str_replace(',', '', $matches[1]);
            }
            // Pattern for "your account balance is TK 2850.0"
            elseif (preg_match('/your account balance is TK\s+([0-9,]+\.?[0-9]*)/i', $fullMessage, $matches)) {
                $simBalance = (float) str_replace(',', '', $matches[1]);
            }
        }
        
        // Ultimate fallback
        if (!$realTransactionId) {
            $realTransactionId = $payload['transaction_id'] ?? $pending->sms_id;
        }
        
        // --- 2. Prepare the Full Message for `load_sim_messages` ---
        $displayNumber = preg_replace('/^88/', '', $recipientNumber);
        if (empty($fullMessage)) {
            $fullMessageText = "Recharge Request of TK {$amount} for mobile no {$displayNumber}, transaction ID {$realTransactionId} is successful.";
            if ($simBalance) $fullMessageText .= " Your account balance is TK {$simBalance}.";
        } else {
            // Clean up the message for GP if it has the +CMT: prefix
            $fullMessageText = preg_replace('/^\+CMT:.*?\n/', '', $fullMessage);
        }
        
        // --- 3. Save to `load_sim_messages` (Full Message) ---
        LoadSimMessages::create([
            'user_id' => $pending->user_id,
            'sim_no' => $pending->sim_number ?? $recipientNumber,
            'operator_company' => $pending->operator_id,
            'message' => $fullMessageText,
            'sender' => 'iRecharge',
            'serial_id' => $pending->sms_id,
            'status' => 1
        ]);
        
        // --- 4. Save to `load_campaign30days` (Real ID + Simple Remarks) ---
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
            'transaction_id' => $realTransactionId, // <-- REAL ID
            'remarks' => 'DELIVERED',              // <-- Simple status
            'status' => 1
        ]);
        
        // --- 5. Update SIM Balance if extracted ---
        if ($simBalance && $pending->operator_id) {
            $this->updateSimBalance($pending->operator_id, $simBalance);
        }
        
        // --- 6. Delete pending record ---
        $pending->delete();
        
        DB::commit();
        
        Log::channel('webhook')->info('Successfully processed transaction', [
            'sms_id' => $pending->sms_id,
            'transaction_id' => $realTransactionId,
            'remarks' => 'DELIVERED'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::channel('webhook')->error('SUCCESS PROCESSING ERROR: ' . $e->getMessage(), [
            'sms_id' => $pending->sms_id,
            'trace' => $e->getTraceAsString()
        ]);
    }
}
    /**
     * Process failed transaction
     */
    private function processFailure($pending, $payload)
    {
        DB::beginTransaction();
        try {
            $statusMessage = $payload['status_message'] ?? 'Recharge Failed';
            $amount = $payload['amount'] ?? $pending->campaign_price;
            $transactionId = $payload['transaction_id'] ?? null;

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
                'campaign_price' => $amount,
                'api_port' => $pending->api_port,
                'transaction_id' => $transactionId,
                'remarks' => "FAILED: {$statusMessage}",
                'status' => 0
            ]);

            LoadSimMessages::create([
                'user_id' => $pending->user_id,
                'sim_no' => $pending->sim_number ?? $pending->targeted_number,
                'operator_company' => $pending->operator_id,
                'message' => "Recharge failed for {$pending->targeted_number}. Amount: TK {$amount}. Reason: {$statusMessage}",
                'sender' => 'iRecharge',
                'serial_id' => $pending->sms_id,
                'status' => 0
            ]);

            $pending->delete();
            DB::commit();

            Log::channel('webhook')->info('Failure - Pending deleted', [
                'sms_id' => $pending->sms_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('webhook')->error('Failure processing failed', [
                'sms_id' => $pending->sms_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Auto-cleanup stuck iRecharge transactions
     * This method can be called via cron or manually
     */
    public function cleanup(Request $request)
    {
        Log::channel('webhook')->info('Cleanup started');
        
        // Get stuck waiting transactions (older than 3 minutes)
        $stuckTransactions = LoadCamPending::where('status', 2)
            ->where('updated_at', '<', now()->subMinutes(3))
            ->get();
        
        $cleaned = 0;
        $results = [];
        
        foreach ($stuckTransactions as $pending) {
            // Check if we have a success message in LoadSimMessages
            $successMessage = LoadSimMessages::where('serial_id', $pending->sms_id)
                ->where(function($q) {
                    $q->where('message', 'LIKE', '%successful%')
                      ->orWhere('message', 'LIKE', '%success%');
                })
                ->orderBy('id', 'desc')
                ->first();
            
            if ($successMessage) {
                // Extract transaction ID from message
                $trx_id = null;
                if (preg_match('/transaction ID\s+([A-Z0-9\.]+)/i', $successMessage->message, $matches)) {
                    $trx_id = $matches[1];
                }
                
                // Extract balance
                $balance = null;
                if (preg_match('/balance is TK\s*([0-9,]+\.?[0-9]*)/i', $successMessage->message, $matches)) {
                    $balance = str_replace(',', '', $matches[1]);
                }
                
                DB::beginTransaction();
                try {
                    // Move to completed
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
                        'transaction_id' => $trx_id ?? $pending->gateway_transaction_id,
                        'remarks' => $successMessage->message,
                        'status' => 1
                    ]);
                    
                    // Update balance if extracted
                    if ($balance) {
                        $simBalance = LoadSimAvailablleBalance::first();
                        if ($simBalance) {
                            $column = $pending->operator_id == 'blink' ? 'blink' : $pending->operator_id;
                            $simBalance->$column = $balance;
                            $simBalance->save();
                        }
                    }
                    
                    // Delete pending
                    $pending->delete();
                    DB::commit();
                    $cleaned++;
                    $results[] = [
                        'sms_id' => $pending->sms_id,
                        'status' => 'cleaned',
                        'transaction_id' => $trx_id
                    ];
                    
                    Log::channel('webhook')->info("Cleanup: cleaned stuck transaction", [
                        'sms_id' => $pending->sms_id
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $results[] = [
                        'sms_id' => $pending->sms_id,
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                // No success message found, mark as failed after 10 minutes
                if ($pending->updated_at < now()->subMinutes(10)) {
                    $pending->update([
                        'status' => 3,
                        'remarks' => 'Auto-failed: No confirmation received'
                    ]);
                    $results[] = [
                        'sms_id' => $pending->sms_id,
                        'status' => 'auto_failed',
                        'waiting_minutes' => now()->diffInMinutes($pending->updated_at)
                    ];
                } else {
                    $results[] = [
                        'sms_id' => $pending->sms_id,
                        'status' => 'still_waiting',
                        'waiting_minutes' => now()->diffInMinutes($pending->updated_at)
                    ];
                }
            }
        }
        
        return response()->json([
            'processed' => $stuckTransactions->count(),
            'cleaned' => $cleaned,
            'results' => $results,
            'timestamp' => now()
        ]);
    }

    /**
     * Extract balance from message
     */
    private function extractBalance($message)
    {
        if (!$message) return null;
        if (preg_match('/balance.*?TK\s*([0-9,]+\.?[0-9]*)/i', $message, $matches)) {
            return (float) str_replace(',', '', $matches[1]);
        }
        return null;
    }

    /**
     * Update SIM balance
     */
    private function updateBalance($operator, $balance)
    {
        try {
            $columnMap = [
                'airtel' => 'airtel',
                'gp' => 'gp',
                'grameenphone' => 'gp',
                'blink' => 'blink',
                'banglalink' => 'blink',
                'robi' => 'robi',
                'teletalk' => 'teletalk',
            ];

            $column = $columnMap[$operator] ?? null;
            if (!$column) return;

            $simBalance = LoadSimAvailablleBalance::first();
            if (!$simBalance) {
                $simBalance = new LoadSimAvailablleBalance();
            }
            
            $simBalance->$column = $balance;
            $simBalance->status = 1;
            $simBalance->save();
        } catch (\Exception $e) {
            Log::channel('webhook')->error('Balance update failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Health check
     */
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'iRecharge Webhook',
            'last_hit' => Cache::get('irecharge_last_webhook_hit'),
            'pending_count' => LoadCamPending::count(),
            'pending_waiting' => LoadCamPending::where('status', 2)->count()
        ]);
    }
}
