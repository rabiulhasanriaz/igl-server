<?php

namespace App\Services;

use GuzzleHttp\Client;

class SSLCommerzService
{
    private $storeId;
    private $storePassword;
    private $apiUrl;
    private $client;

    public function __construct()
    {
        // DIRECT LIVE CREDENTIALS
        $this->storeId = "felnatech0live";
        $this->storePassword = "6815E61C133C384115";

        // DIRECT LIVE API URL
        $this->apiUrl = "https://securepay.sslcommerz.com";

        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // Disable SSL verification for cPanel localhost issues
        ]);
    }

    public function initiatePayment($postData)
    {
        $url = $this->apiUrl . "/gwprocess/v4/api.php";

        try {
            $response = $this->client->post($url, [
                'form_params' => $postData,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);

            $responseData = json_decode($response->getBody(), true);

            \Log::info('SSLCommerz Initiate Payment Response', $responseData);

            return $responseData;

        } catch (\Exception $e) {
            \Log::error('SSLCommerz Initiate Payment Error: ' . $e->getMessage());
            return false;
        }
    }

    public function validatePayment($requestData, $transactionId, $amount)
    {
        if (!isset($requestData['tran_id']) ||
            !isset($requestData['amount']) ||
            !isset($requestData['status'])) {
            return false;
        }

        if ($requestData['tran_id'] != $transactionId ||
            $requestData['amount'] != $amount) {
            \Log::warning('SSLCommerz Validation Failed: Data mismatch', [
                'expected_tran_id' => $transactionId,
                'received_tran_id' => $requestData['tran_id'],
                'expected_amount' => $amount,
                'received_amount' => $requestData['amount']
            ]);
            return false;
        }

        // LIVE VALIDATION
        if ($requestData['status'] === 'VALID' && isset($requestData['val_id'])) {

            $verifyUrl = $this->apiUrl . "/validator/api/validationserverAPI.php" .
                "?val_id={$requestData['val_id']}" .
                "&store_id={$this->storeId}" .
                "&store_passwd={$this->storePassword}" .
                "&v=1&format=json";

            try {
                $response = $this->client->get($verifyUrl, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
                    ]
                ]);

                $data = json_decode($response->getBody(), true);

                \Log::info('SSLCommerz Validation Response', $data);

                return $data['status'] === 'VALID' || $data['status'] === 'VALIDATED';

            } catch (\Exception $e) {
                \Log::error('SSLCommerz Validation Error: ' . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    public function getPaymentPostData($data)
    {
        return [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,

            'total_amount' => $data['total_amount'],
            'currency' => $data['currency'] ?? 'BDT',
            'tran_id' => $data['tran_id'],

            'success_url' => $data['success_url'],
            'fail_url' => $data['fail_url'],
            'cancel_url' => $data['cancel_url'],
            'ipn_url' => $data['ipn_url'] ?? '',

            'product_name' => $data['product_name'],
            'product_category' => $data['product_category'],
            'product_profile' => $data['product_profile'],

            'cus_name' => $data['cus_name'],
            'cus_email' => $data['cus_email'],
            'cus_add1' => $data['cus_add1'] ?? 'N/A',
            'cus_add2' => $data['cus_add2'] ?? 'N/A',
            'cus_city' => $data['cus_city'] ?? 'N/A',
            'cus_state' => $data['cus_state'] ?? 'N/A',
            'cus_postcode' => $data['cus_postcode'] ?? 'N/A',
            'cus_country' => $data['cus_country'] ?? 'Bangladesh',
            'cus_phone' => $data['cus_phone'],

            'shipping_method' => $data['shipping_method'] ?? 'NO',
            'num_of_item' => $data['num_of_item'] ?? 1,
            'emi_option' => $data['emi_option'] ?? 0,

            // Extra values
            'value_a' => 'user_id_' . (auth()->id() ?? '0'),
            'value_b' => 'balance_topup',
            'value_c' => 'sms_service',
        ];
    }
}

