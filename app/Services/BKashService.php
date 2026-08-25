<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BKashService
{
    private $baseUrl;
    private $username;
    private $password;
    private $appKey;
    private $appSecret;
    private $isSandbox;

    public function __construct()
    {
        $this->isSandbox = false; // LIVE MODE
        $this->baseUrl = $this->isSandbox
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';

        $this->username   = '01914445932';
        $this->password   = 'd%RA>3p_oFO';
        $this->appKey     = 'm8pRArRcLuLG21OuhDva0Zhftc';
        $this->appSecret  = 'i7SWG2W64qEmwtxxoADJ8p7IhIi8zIs1ZcoOufXU7uRCb3RBEaP8';
        
        $this->initializeTokenTable();
    }

    public function isSandbox()
    {
        return $this->isSandbox;
    }

    private function initializeTokenTable()
    {
        if (!Schema::hasTable('bkash_token')) {
            Schema::create('bkash_token', function ($table) {
                $table->boolean('sandbox_mode')->primary();
                $table->bigInteger('id_expiry')->notNullable();
                $table->text('id_token')->notNullable();
                $table->bigInteger('refresh_expiry')->notNullable();
                $table->text('refresh_token')->notNullable();
                $table->timestamps();
            });

            DB::table('bkash_token')->insert([
                [
                    'sandbox_mode' => 1, 
                    'id_expiry' => 0, 
                    'id_token' => 'id_token', 
                    'refresh_expiry' => 0, 
                    'refresh_token' => 'refresh_token',
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'sandbox_mode' => 0, 
                    'id_expiry' => 0, 
                    'id_token' => 'id_token', 
                    'refresh_expiry' => 0, 
                    'refresh_token' => 'refresh_token',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }

    private function curlWithBody($url, $header, $method, $body)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error: " . $err);
        }

        return $response;
    }

    private function getIdTokenFromRefreshToken($refresh_token)
    {
        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array(
            'app_key' => $this->appKey, 
            'app_secret' => $this->appSecret, 
            'refresh_token' => $refresh_token
        );

        $response = $this->curlWithBody(
            '/tokenized/checkout/token/refresh', 
            $header, 
            'POST', 
            json_encode($body_data)
        );

        $responseData = json_decode($response);
        
        if (isset($responseData->id_token)) {
            return $responseData->id_token;
        }

        throw new \Exception("Failed to refresh token: " . ($responseData->error_message ?? 'Unknown error'));
    }

    public function grant()
    {
        $sandbox = $this->isSandbox ? 1 : 0;
        
        $tokenData = DB::table('bkash_token')->where('sandbox_mode', $sandbox)->first();

        if ($tokenData) {
            $idExpiry = $tokenData->id_expiry;
            $idToken = $tokenData->id_token;
            $refreshExpiry = $tokenData->refresh_expiry;
            $refreshToken = $tokenData->refresh_token;
            
            if ($idExpiry > time()) {
                return $idToken;
            }
            
            if ($refreshExpiry > time()) {
                $idToken = $this->getIdTokenFromRefreshToken($refreshToken);
                
                DB::table('bkash_token')
                    ->where('sandbox_mode', $sandbox)
                    ->update([
                        'id_expiry' => time() + 3600,
                        'id_token' => $idToken,
                        'updated_at' => now()
                    ]);
                
                return $idToken;
            }
        }

        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array(
            'app_key' => $this->appKey, 
            'app_secret' => $this->appSecret
        );

        $response = $this->curlWithBody(
            '/tokenized/checkout/token/grant', 
            $header, 
            'POST', 
            json_encode($body_data)
        );

        $responseData = json_decode($response);
        
        if (!isset($responseData->id_token)) {
            throw new \Exception("Grant API failed: " . ($responseData->error_message ?? 'Unknown error'));
        }

        $idToken = $responseData->id_token;
        $refreshToken = $responseData->refresh_token ?? '';

        DB::table('bkash_token')
            ->where('sandbox_mode', $sandbox)
            ->update([
                'id_expiry' => time() + 3600,
                'id_token' => $idToken,
                'refresh_expiry' => time() + 864000,
                'refresh_token' => $refreshToken,
                'updated_at' => now()
            ]);

        return $idToken;
    }

    public function getToken()
    {
        return $this->grant();
    }

    public function createPayment($amount, $invoiceNumber, $callbackUrl)
    {
        try {
            $url = '/tokenized/checkout/create';
            $token = $this->getToken();

            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $token,
                'X-APP-Key:' . $this->appKey
            );

            $body_data = array(
                'mode' => '0011',
                'payerReference' => 'user_' . auth()->id(),
                'callbackURL' => $callbackUrl,
                'amount' => (string)$amount,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $invoiceNumber
            );

            Log::info('Creating bKash payment', $body_data);

            $response = $this->curlWithBody($url, $header, 'POST', json_encode($body_data));
            $responseData = json_decode($response, true);

            Log::info('bKash payment creation response', ['response' => $responseData]);

            if (isset($responseData['bkashURL'], $responseData['paymentID'])) {
                return [
                    'success' => true,
                    'paymentID' => $responseData['paymentID'],
                    'bkashURL' => $responseData['bkashURL'],
                    'transactionStatus' => $responseData['transactionStatus'] ?? null
                ];
            }

            $errorMessage = $responseData['errorMessage'] ?? 
                           $responseData['statusMessage'] ?? 
                           $responseData['message'] ?? 
                           'Payment creation failed';

            return [
                'success' => false,
                'error' => $errorMessage,
                'raw_response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('bKash payment creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'invoice' => $invoiceNumber
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function executePayment($paymentID)
    {
        try {
            $url = '/tokenized/checkout/execute';
            $token = $this->getToken();

            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $token,
                'X-APP-Key:' . $this->appKey
            );

            $body_data = array('paymentID' => $paymentID);

            $response = $this->curlWithBody($url, $header, 'POST', json_encode($body_data));
            $responseData = json_decode($response, true);
            
            Log::info('bKash payment execution response', [
                'paymentID' => $paymentID,
                'response' => $responseData
            ]);

            return $responseData;

        } catch (\Exception $e) {
            Log::error('bKash payment execution failed', [
                'error' => $e->getMessage(),
                'paymentID' => $paymentID
            ]);
            throw $e;
        }
    }

    public function isPaymentSuccessful($paymentData)
    {
        return isset($paymentData['transactionStatus']) &&
               $paymentData['transactionStatus'] === 'Completed';
    }

    public function getCallbackUrl()
    {
        // Use the named route to generate the correct URL
        return route('user.balance.bkash.callback');
    }

    public function getTokenStatus()
    {
        $sandbox = $this->isSandbox ? 1 : 0;
        $tokenData = DB::table('bkash_token')->where('sandbox_mode', $sandbox)->first();
        
        if ($tokenData) {
            return [
                'id_token_valid' => $tokenData->id_expiry > time(),
                'id_expiry_in' => $tokenData->id_expiry - time(),
                'refresh_token_valid' => $tokenData->refresh_expiry > time(),
                'refresh_expiry_in' => $tokenData->refresh_expiry - time(),
                'sandbox_mode' => $this->isSandbox,
                'mode' => 'LIVE'
            ];
        }
        
        return ['error' => 'No token data found'];
    }
}