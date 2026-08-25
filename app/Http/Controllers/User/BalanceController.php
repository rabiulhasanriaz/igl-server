<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\AccSmsBalance;
use App\Services\SSLCommerzService;
use App\Services\BKashService;

class BalanceController extends Controller
{
    protected $sslCommerz;
    protected $bkash;

    public function __construct()
    {
        $this->sslCommerz = new SSLCommerzService();
        $this->bkash = new BKashService();
    }

    /**
     * Calculate service charge based on gateway
     */
    private function calculateServiceCharge($amount, $gateway = 'sslcommerz')
    {
        // Different service charges for different gateways
        if ($gateway === 'bkash') {
            $serviceChargePercentage = 2; // 2% for bKash
        } else {
            $serviceChargePercentage = 2.5; // 2.5% for SSLCommerz
        }
        
        $serviceCharge = ($amount * $serviceChargePercentage) / 100;
        $totalAmount = $amount + $serviceCharge;
        
        return [
            'original_amount' => $amount,
            'service_charge_percentage' => $serviceChargePercentage,
            'service_charge' => $serviceCharge,
            'total_amount' => $totalAmount
        ];
    }

    public function showTopUpForm()
    {
        $user = auth()->user();
        
        $recentTopups = AccSmsBalance::where('asb_pay_to', $user->id)
            ->where('asb_deal_type', 1)
            ->orderBy('asb_submit_time', 'desc')
            ->limit(10)
            ->get();

        $bkashService = new BKashService();
        $bkashSandbox = $bkashService->isSandbox();

        return view('user.balance.topup', compact('recentTopups', 'bkashSandbox'));
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10',
            'gateway' => 'required|in:sslcommerz,bkash'
        ], [
            'amount.min' => 'Minimum top-up amount is ৳10',
            'gateway.required' => 'Please select a payment gateway'
        ]);

        $requestedAmount = $request->amount;
        $gateway = $request->gateway;
        $user = auth()->user();

        // Calculate service charge based on gateway
        $chargeCalculation = $this->calculateServiceCharge($requestedAmount, $gateway);

        if ($gateway === 'sslcommerz') {
            return $this->initiateSSLCommerzPayment($user, $requestedAmount, $chargeCalculation);
        } elseif ($gateway === 'bkash') {
            return $this->initiateBKashPayment($user, $requestedAmount, $chargeCalculation);
        }

        return redirect()->back()->with('error', 'Invalid payment gateway selected.');
    }

    /**
     * Initiate SSLCommerz Payment
     */
    private function initiateSSLCommerzPayment($user, $amount, $chargeCalculation)
    {
        // Generate unique transaction ID
        $tran_id = 'TUP_' . uniqid() . '_' . time();

        // Prepare payment data
        $postData = $this->sslCommerz->getPaymentPostData([
            'total_amount' => $chargeCalculation['total_amount'],
            'tran_id' => $tran_id,
            'success_url' => route('user.balance.success'),
            'fail_url' => route('user.balance.fail'),
            'cancel_url' => route('user.balance.cancel'),
            'ipn_url' => route('user.balance.ipn'),

            'product_name' => "SMS Balance Top Up",
            'product_category' => "SMS Service",
            'product_profile' => "general",

            'cus_name' => $user->name,
            'cus_email' => $user->email,
            'cus_phone' => $user->phone ?? "01XXXXXXXXX",
        ]);

        // Store transaction in session
        session([
            'payment_gateway' => 'sslcommerz',
            'ssl_tran_id' => $tran_id,
            'ssl_amount' => $amount,
            'ssl_total_amount' => $chargeCalculation['total_amount'],
            'ssl_service_charge' => $chargeCalculation['service_charge'],
            'ssl_service_charge_percentage' => $chargeCalculation['service_charge_percentage'],
            'ssl_user_id' => $user->id
        ]);

        // Initiate payment
        $paymentResponse = $this->sslCommerz->initiatePayment($postData);

        if ($paymentResponse && isset($paymentResponse['status']) && $paymentResponse['status'] == 'SUCCESS') {
            return redirect($paymentResponse['GatewayPageURL']);
        } else {
            $errorMessage = $paymentResponse['failedreason'] ?? 'Payment initiation failed. Please try again.';
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Initiate bKash Payment
     */
    private function initiateBKashPayment($user, $amount, $chargeCalculation)
    {
        try {
            // Generate unique invoice number
            $invoiceNumber = 'BKASH_' . uniqid() . '_' . time();

            // Create payment in bKash
            $paymentResponse = $this->bkash->createPayment(
                $chargeCalculation['total_amount'],
                $invoiceNumber,
                $this->bkash->getCallbackUrl()
            );

            if ($paymentResponse['success']) {
                // Store transaction in session
                session([
                    'payment_gateway' => 'bkash',
                    'bkash_payment_id' => $paymentResponse['paymentID'],
                    'bkash_invoice_number' => $invoiceNumber,
                    'bkash_amount' => $amount,
                    'bkash_total_amount' => $chargeCalculation['total_amount'],
                    'bkash_service_charge' => $chargeCalculation['service_charge'],
                    'bkash_service_charge_percentage' => $chargeCalculation['service_charge_percentage'],
                    'bkash_user_id' => $user->id
                ]);

                // Redirect to bKash payment page
                return redirect($paymentResponse['bkashURL']);
            } else {
                return redirect()->back()->with('error', 'bKash Payment initiation failed: ' . $paymentResponse['error']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'bKash Payment error: ' . $e->getMessage());
        }
    }

    /**
     * bKash Callback Handler
     */
    public function bkashCallback(Request $request)
    {
        $paymentID = $request->input('paymentID');
        $status = $request->input('status');

        // Retrieve session data
        $sessionPaymentID = session('bkash_payment_id');
        $amount = session('bkash_amount');
        $total_amount = session('bkash_total_amount');
        $service_charge = session('bkash_service_charge');
        $service_charge_percentage = session('bkash_service_charge_percentage');
        $user_id = session('bkash_user_id');

        if ($paymentID !== $sessionPaymentID) {
            return redirect()->route('user.balance.topup')->with('error', 'Invalid payment session.');
        }

        if ($status === 'success') {
            try {
                // Execute payment
                $paymentData = $this->bkash->executePayment($paymentID);

                if ($this->bkash->isPaymentSuccessful($paymentData)) {
                    // Get the user
                    $user = \App\Model\User::find($user_id);
                    $paid_by = $user ? $user->create_by : null;

                    DB::transaction(function () use ($user_id, $paid_by, $amount, $paymentID, $service_charge, $total_amount, $service_charge_percentage, $paymentData) {
                        AccSmsBalance::create([
                            'asb_paid_by' => $paid_by,
                            'asb_pay_to' => $user_id,
                            'asb_pay_ref' => $paymentID,
                            'asb_credit' => $amount,
                            'asb_debit' => 0,
                            'asb_submit_time' => now(),
                            'asb_target_time' => now(),
                            'asb_pay_mode' => 3, // 3 for bKash
                            'asb_payment_status' => 1,
                            'asb_deal_type' => 1,
                            'credit_return_type' => 'balance',
                            'asb_service_charge' => $service_charge,
                            'asb_service_charge_percentage' => $service_charge_percentage,
                            'asb_total_paid' => $total_amount,
                            'asb_gateway_response' => json_encode($paymentData),
                        ]);
                    });

                    // Clear session
                    session()->forget([
                        'payment_gateway', 'bkash_payment_id', 'bkash_invoice_number',
                        'bkash_amount', 'bkash_total_amount', 'bkash_service_charge', 
                        'bkash_service_charge_percentage', 'bkash_user_id'
                    ]);

                    return redirect()->route('user.balance.topup')->with('success', 
                        "Balance topped up successfully via bKash! Credited: ৳{$amount} (Service charge: ৳{$service_charge} - {$service_charge_percentage}%)"
                    );
                } else {
                    return redirect()->route('user.balance.topup')->with('error', 
                        'bKash payment execution failed: ' . ($paymentData['errorMessage'] ?? 'Unknown error')
                    );
                }
            } catch (\Exception $e) {
                return redirect()->route('user.balance.topup')->with('error', 'bKash payment error: ' . $e->getMessage());
            }
        } else {
            // Payment failed or cancelled
            session()->forget([
                'payment_gateway', 'bkash_payment_id', 'bkash_invoice_number',
                'bkash_amount', 'bkash_total_amount', 'bkash_service_charge', 
                'bkash_service_charge_percentage', 'bkash_user_id'
            ]);

            if ($status === 'cancel') {
                return redirect()->route('user.balance.topup')->with('warning', 'bKash payment was cancelled.');
            } else {
                return redirect()->route('user.balance.topup')->with('error', 'bKash payment failed.');
            }
        }
    }

    public function paymentSuccess(Request $request)
    {
        $tran_id = session('ssl_tran_id');
        $amount = session('ssl_amount'); // Original amount to credit
        $total_amount = session('ssl_total_amount'); // Total amount customer paid
        $service_charge = session('ssl_service_charge');
        $service_charge_percentage = session('ssl_service_charge_percentage');
        $user_id = session('ssl_user_id');

        if (!$tran_id || !$amount) {
            return redirect()->route('user.balance.topup')->with('error', 'Invalid transaction.');
        }

        // Verify payment - use total amount for verification
        $validation = $this->sslCommerz->validatePayment($request->all(), $tran_id, $total_amount);

        if ($validation) {
            // Get the user
            $user = \App\Model\User::find($user_id);

            // Use the creator (create_by) as the payer
            $paid_by = $user ? $user->create_by : null;

            DB::transaction(function () use ($user_id, $paid_by, $amount, $tran_id, $service_charge, $total_amount, $service_charge_percentage) {
                AccSmsBalance::create([
                    'asb_paid_by' => $paid_by,   // Creator of this user
                    'asb_pay_to' => $user_id,    // Current user receiving balance
                    'asb_pay_ref' => $tran_id,
                    'asb_credit' => $amount,     // Original amount credited
                    'asb_debit' => 0,
                    'asb_submit_time' => now(),
                    'asb_target_time' => now(),
                    'asb_pay_mode' => 2,
                    'asb_payment_status' => 1,
                    'asb_deal_type' => 1,
                    'credit_return_type' => 'balance',
                    // Store service charge information
                    'asb_service_charge' => $service_charge,
                    'asb_service_charge_percentage' => $service_charge_percentage,
                    'asb_total_paid' => $total_amount,
                ]);
            });

            // Clear session
            session()->forget([
                'payment_gateway', 'ssl_tran_id', 'ssl_amount', 'ssl_total_amount', 
                'ssl_service_charge', 'ssl_service_charge_percentage', 'ssl_user_id'
            ]);

            return redirect()->route('user.balance.topup')->with('success', 
                "Balance topped up successfully! Credited: ৳{$amount} (Service charge: ৳{$service_charge} - {$service_charge_percentage}%)"
            );
        } else {
            return redirect()->route('user.balance.topup')->with('error', 'Payment verification failed.');
        }
    }

    public function paymentFail(Request $request)
    {
        // Log failed payment attempt
        \Log::info('Payment failed', ['data' => $request->all(), 'session' => session()->all()]);
        
        session()->forget([
            'payment_gateway', 'ssl_tran_id', 'ssl_amount', 'ssl_total_amount', 
            'ssl_service_charge', 'ssl_service_charge_percentage', 'ssl_user_id'
        ]);
        return redirect()->route('user.balance.topup')->with('error', 'Payment failed. Please try again.');
    }

    public function paymentCancel(Request $request)
    {
        session()->forget([
            'payment_gateway', 'ssl_tran_id', 'ssl_amount', 'ssl_total_amount', 
            'ssl_service_charge', 'ssl_service_charge_percentage', 'ssl_user_id'
        ]);
        return redirect()->route('user.balance.topup')->with('warning', 'Payment cancelled.');
    }

    public function paymentIPN(Request $request)
    {
        // Handle Instant Payment Notification
        \Log::info('IPN received', ['data' => $request->all()]);
        
        // You can implement IPN logic here for additional security
        return response()->json(['status' => 'received']);
    }
}
