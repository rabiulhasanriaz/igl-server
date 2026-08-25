<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoadSimMessages extends Model
{
    protected $fillable = [
        'user_id', 'sim_no', 'operator_company', 'message', 'sender', 'serial_id', 'status'
    ];
    protected $dates = [
        'created_at',
    ];

    public function user_name(){
        return $this->belongsTo('App\Model\User','user_id','id');
    }

    public static function getTransactionIdFromMessage($total_message, $opcompany = null)
    {
        try {
            // ===== NEW: iRecharge format =====
            // Example: "Recharge Request of TK 20.00 for mobile no 01965838328, transaction ID R260520.1158.250040 is successful."
            if (strpos($total_message, 'transaction ID R') !== false) {
                $message_with_trx_id = explode('transaction ID R', $total_message)[1];
                $trx_id = 'R' . explode(' ', $message_with_trx_id)[0];
                if (!empty($trx_id) && $trx_id != 'R') {
                    return $trx_id;
                }
            }
            
            // Also check for lowercase 'transaction id r'
            if (strpos($total_message, 'transaction id r') !== false) {
                $message_with_trx_id = explode('transaction id r', $total_message)[1];
                $trx_id = 'R' . explode(' ', $message_with_trx_id)[0];
                if (!empty($trx_id) && $trx_id != 'R') {
                    return $trx_id;
                }
            }
            
            // Pattern match for R followed by numbers and dots
            if (preg_match('/R\d{6}\.\d{4}\.[0-9a-f]+/', $total_message, $matches)) {
                return $matches[0];
            }
            
            // ===== Existing formats =====
            if (strpos($total_message, 'transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('transaction ID ', $total_message)[1];
                $trx_id = explode(' ', $message_with_trx_id)[0];
                if (!empty($trx_id)) {
                    return $trx_id;
                }
            }
            
            if (strpos($total_message, 'Transaction ID is ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];
                $trx_id = explode(' ', $message_with_trx_id)[0];
                if (!empty($trx_id)) {
                    return $trx_id;
                }
            }
            
            if (strpos($total_message, 'Transaction ID ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction ID ', $total_message)[1];
                $trx_id = explode(' ', $message_with_trx_id)[0];
                if (!empty($trx_id)) {
                    return $trx_id;
                }
            }
            
            if (strpos($total_message, 'Transaction number is ') !== false) {
                /*robi/airtel format*/
                $message_with_trx_id = explode('Transaction number is ', $total_message)[1];
                $trx_id = explode(' ', $message_with_trx_id)[0];
                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
                if (!empty($trx_id)) {
                    return $trx_id;
                }
            }
            
            if (strpos($total_message, 'Transaction number ') !== false) {
                /*gp and bl format*/
                $message_with_trx_id = explode('Transaction number ', $total_message)[1];
                $trx_id = explode(' ', $message_with_trx_id)[0];
                if (!empty($trx_id)) {
                    return $trx_id;
                }
            }
            
            if (strpos($total_message, 'Transaction ID is ') !== false) {
                /*teletalk format*/
                $message_with_trx_id = explode('Transaction ID is ', $total_message)[1];
                $trx_id = explode(' ', $message_with_trx_id)[0];
                if (substr($trx_id, -4) == 'Your') {
                    $trx_id = substr($trx_id, 0, -4);
                }
                if (!empty($trx_id)) {
                    return $trx_id;
                }
            }
            
            return "";
            
        } catch (\Exception $e) {
            \Log::error('Error extracting transaction ID: ' . $e->getMessage());
            return "";
        }
    }
}