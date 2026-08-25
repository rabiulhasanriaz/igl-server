<?php

use GuzzleHttp\Client;
use App\Model\AccSmsRate;
use App\Model\SenderIdRegister;
use App\Model\UserDetail;
use App\Model\SmsDesktopTemplate;


class SmsHelper
{
    /*check a string is unicode or text*/
    public static function is_unicode($string)
    {
        if (strlen($string) != strlen(utf8_decode($string))) {
            return true;
        } else {
            return false;
        }
    }

    public static function main_test_api($uid,$otp){
        // $hash_otp = implode('#',str_split($otp)); 
        // dd($hash_otp);
        $template = UserDetail::where('user_id',$uid)->first();
        $temp = explode(',',$template->template_permission);
        $template_det = SmsDesktopTemplate::whereIn('id',$temp)->get();
        // shuffle($template_det);
        
            
            foreach ($template_det as $temp_per) {
                        $replace[] = str_replace("YOUROTPKEY",$otp,$temp_per->template_content);   
                        shuffle($replace);
            }
        // dd($template_det);
        // dd($replace);
        // return $replace;
        
        //echo '<pre>';
        return $replace[0] ;
    }

    /*unicode sms count*/
    public static function unicode_sms_count($string)
    {
        $strLength = mb_strlen(urldecode($string));
        // $strLength = mb_strlen(urldecode($string));

        /*get sms count*/
        if ($strLength <= 70) {
            $smsCount = 1;
        } else {
            $smsCount = ceil($strLength / 67);
        }
        return $smsCount;
    }

    /*text sms count*/
    public static function text_sms_count($string)
    {
        // return urldecode($string);
        $strLength = mb_strlen(urldecode($string));
        // $strLength = mb_strlen(urldecode($string));
        /*get sms count*/
        if ($strLength <= 160) {
            $smsCount = 1;
        } else {
            $smsCount = ceil($strLength / 153);
        }
        return $smsCount;
    }


    /*check sender id is masking or not*/
public static function isMasking($sender_ids_id)
{
    $senderId = SenderIdRegister::select('sir_sender_id')
                ->where('id', $sender_ids_id)
                ->first();

    if (!$senderId) {
        return true; // Default to masking if sender ID not found
    }

    if (is_numeric($senderId->sir_sender_id)) {
        // Check if it's a special 8809 sender ID
        if (strpos($senderId->sir_sender_id, '8809') === 0) {
            return 'iptsp'; // Special case
        }
        return false; // Regular numeric case
    }
    
    return true; // Non-numeric is masking
}

    /*send non masking single sms*/
    // public static function send_non_masking_single_sms($user_name, $password, $sms_text, $number, $sender)
    public static function send_non_masking_single_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $msgType, $transType)
    {
        // foreach ($number as $key => $value) {
        //     if (empty($value)) {
        //         unset($number[$key]);
        //     }
        // }
        // if (($user_name == '') || ($password == '') || ($sms_text == '') || empty($number) || ($sender == '')) {
        //     return "0150";/*something was missing*/
        // } else {
        //     $client = new Client();
        //     $numbers = implode(',', $number);
        //     $sms_text = urlencode($sms_text);

        //     if (self::is_unicode($sms_text)) {
        //         $url = "http://54.247.191.102/api/v3/sendsms/plain?user=" . $user_name . "&password=" . $password . "&SMSText=" . $sms_text . "&GSM=" . $numbers . "&sender=" . $sender . "&type=longSMS";
        //     } else {
        //         $url = "http://54.247.191.102/api/v3/sendsms/plain?user=" . $user_name . "&password=" . $password . "&SMSText=" . $sms_text . "&GSM=" . $numbers . "&sender=" . $sender . "&type=longSMS&datacoding=8";
        //     }

        //     try {
        //         $res = $client->request('GET', $url);

        //         $ret = $res->getBody();

        //         $xml_response = simplexml_load_string($ret);
        //         return $xml_response->result;
        //     }catch (Exception $e) {
        //         return "0160";
        //     }
        // }
    }


    // =================================================================
    // ========================Send GP SMS START========================
    // =================================================================
    
       public static function ra_non_masking_iptsp_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $transType, $msgType)
    {
        try {
            $number = array_filter($number); // Remove empty numbers

            if (empty($user_name) || empty($password) || empty($sms_text) || empty($number)) {
                return "9001"; // Required field missing
            }

            $url = "https://api.mnpspbd.com/a2p-sms-iptsp/api/v1/send-sms";

            $headers = [
                'Content-Type: application/json',
            ];

            $data = [
                'username' => $user_name,
                'password' => $password,
                'billMsisdn' => $billMsisdn,
                'apiKey' => $apiKey,
                'cli' => $sender,
                'msisdnList' => $number,
                'transactionType' => $transType,
                'messageType' => $msgType,
                'message' => $sms_text
            ];

            //  dd($data);

            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                return "9099"; // Server failure
            } else {
                $response_data = json_decode($response, true);

                $serverResponseCode = $response_data['serverResponseCode'] ?? null;
                $serverTxnId = $response_data['serverTxnId'] ?? null;
                $serverResponseMessage = $response_data['serverResponseMessage'] ?? null;
                $mnoTxnId = $response_data['mnoTxnId'] ?? null;
                $mnoResponseCode = $response_data['mnoResponseCode'] ?? null;
                $mnoResponseMessage = $response_data['mnoResponseMessage'] ?? null;

                $xml_response = [
                    'serverTxnId' => $serverTxnId,
                    'serverResponseCode' => $serverResponseCode,
                    'serverResponseMessage' => $serverResponseMessage,
                    'mnoTxnId' => $mnoTxnId,
                    'mnoResponseCode' => $mnoResponseCode,
                    'mnoResponseMessage' => $mnoResponseMessage
                ];

                return $xml_response;
            }
        } catch (Exception $e) {
            return "0160";
        }
    }
        public static function ra_non_masking_infozillion_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $transType, $msgType)
    {
        try {
            $number = array_filter($number); // Remove empty numbers

            if (empty($user_name) || empty($password) || empty($sms_text) || empty($number)) {
                return "9001"; // Required field missing
            }

            $url = "https://api.mnpspbd.com/a2p-sms/api/v1/send-sms";

            $headers = [
                'Content-Type: application/json',
            ];

            $data = [
                'username' => $user_name,
                'password' => $password,
                'billMsisdn' => $billMsisdn,
                'apiKey' => $apiKey,
                'cli' => $sender,
                'msisdnList' => $number,
                'transactionType' => $transType,
                'messageType' => $msgType,
                'message' => $sms_text
            ];

            //  dd($data);

            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                return "9099"; // Server failure
            } else {
                $response_data = json_decode($response, true);

                $serverResponseCode = $response_data['serverResponseCode'] ?? null;
                $serverTxnId = $response_data['serverTxnId'] ?? null;
                $serverResponseMessage = $response_data['serverResponseMessage'] ?? null;
                $mnoTxnId = $response_data['mnoTxnId'] ?? null;
                $mnoResponseCode = $response_data['mnoResponseCode'] ?? null;
                $mnoResponseMessage = $response_data['mnoResponseMessage'] ?? null;

                $xml_response = [
                    'serverTxnId' => $serverTxnId,
                    'serverResponseCode' => $serverResponseCode,
                    'serverResponseMessage' => $serverResponseMessage,
                    'mnoTxnId' => $mnoTxnId,
                    'mnoResponseCode' => $mnoResponseCode,
                    'mnoResponseMessage' => $mnoResponseMessage
                ];

                return $xml_response;
            }
        } catch (Exception $e) {
            return "0160";
        }
    }
    // ******************************************************************
    // ************************Send GP SMS END***************************
    // ******************************************************************

    
    // =================================================================
    // ========================Send GP SMS START========================
    // =================================================================
    public static function gp_masking_infozillion_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $transType, $msgType)
    {
        try {
            $number = array_filter($number); // Remove empty numbers

            if (empty($user_name) || empty($password) || empty($sms_text) || empty($number)) {
                return "9001"; // Required field missing
            }

            $url = "https://api.mnpspbd.com/a2p-sms/api/v1/send-sms";

            $headers = [
                'Content-Type: application/json',
            ];

            $data = [
                'username' => $user_name,
                'password' => $password,
                'billMsisdn' => $billMsisdn,
                'apiKey' => $apiKey,
                'cli' => $sender,
                'msisdnList' => $number,
                'transactionType' => $transType,
                'messageType' => $msgType,
                'message' => $sms_text
            ];

            // print_r($data);

            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            // dd($response);

            if ($response === false) {
                return "9099"; // Server failure
            } else {
                $response_data = json_decode($response, true);

                $serverResponseCode = $response_data['serverResponseCode'] ?? null;
                $serverTxnId = $response_data['serverTxnId'] ?? null;
                $serverResponseMessage = $response_data['serverResponseMessage'] ?? null;
                $mnoTxnId = $response_data['mnoTxnId'] ?? null;
                $mnoResponseCode = $response_data['mnoResponseCode'] ?? null;
                $mnoResponseMessage = $response_data['mnoResponseMessage'] ?? null;

                $xml_response = [
                    'serverTxnId' => $serverTxnId,
                    'serverResponseCode' => $serverResponseCode,
                    'serverResponseMessage' => $serverResponseMessage,
                    'mnoTxnId' => $mnoTxnId,
                    'mnoResponseCode' => $mnoResponseCode,
                    'mnoResponseMessage' => $mnoResponseMessage
                ];

                return $xml_response;
            }
        } catch (Exception $e) {
            return "0160";
        }
    }
    // ******************************************************************
    // ************************Send GP SMS END***************************
    // ******************************************************************


    // =================================================================
    // ===================Send Robi/Airtel SMS START====================
    // =================================================================
  public static function ra_masking_infozillion_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $msgType, $transType)
    {
        try {
            $number = array_filter($number); // Remove empty numbers

            if (empty($user_name) || empty($password) || empty($sms_text) || empty($number)) {
                return "9001"; // Required field missing
            }

            $url = "https://api.mnpspbd.com/a2p-sms/api/v1/send-sms";

            $headers = [
                'Content-Type: application/json',
            ];

            $data = [
                'username' => $user_name,
                'password' => $password,
                'billMsisdn' => $billMsisdn,
                'apiKey' => $apiKey,
                'cli' => $sender,
                'msisdnList' => $number,
                'transactionType' => $transType,
                'messageType' => $msgType,
                'message' => $sms_text
            ];

            // dd($data);

            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                return "9099"; // Server failure
            } else {
                $response_data = json_decode($response, true);

                $serverResponseCode = $response_data['serverResponseCode'] ?? null;
                $serverTxnId = $response_data['serverTxnId'] ?? null;
                $serverResponseMessage = $response_data['serverResponseMessage'] ?? null;
                $mnoTxnId = $response_data['mnoTxnId'] ?? null;
                $mnoResponseCode = $response_data['mnoResponseCode'] ?? null;
                $mnoResponseMessage = $response_data['mnoResponseMessage'] ?? null;

                $xml_response = [
                    'serverTxnId' => $serverTxnId,
                    'serverResponseCode' => $serverResponseCode,
                    'serverResponseMessage' => $serverResponseMessage,
                    'mnoTxnId' => $mnoTxnId,
                    'mnoResponseCode' => $mnoResponseCode,
                    'mnoResponseMessage' => $mnoResponseMessage
                ];

                return $xml_response;
            }
        } catch (Exception $e) {
            return "0160";
        }
    }
    // ******************************************************************
    // ******************Send Robi/Airtel SMS END************************
    // ******************************************************************

    // =================================================================
    // ===================Send Robi/Airtel SMS START====================
    // =================================================================
    public static function bl_masking_infozillion_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $msgType, $transType)
    {
        try {
            $number = array_filter($number); // Remove empty numbers

            if (empty($user_name) || empty($password) || empty($sms_text) || empty($number)) {
                return "9001"; // Required field missing
            }

            $url = "https://api.mnpspbd.com/a2p-sms/api/v1/send-sms";

            $headers = [
                'Content-Type: application/json',
            ];

            $data = [
                'username' => $user_name,
                'password' => $password,
                'billMsisdn' => $billMsisdn,
                'apiKey' => $apiKey,
                'cli' => $sender,
                'msisdnList' => $number,
                'transactionType' => $transType,
                'messageType' => $msgType,
                'message' => $sms_text
            ];

            // print_r($data);

            $data_json = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                return "9099"; // Server failure
            } else {
                $response_data = json_decode($response, true);

                $serverResponseCode = $response_data['serverResponseCode'] ?? null;
                $serverTxnId = $response_data['serverTxnId'] ?? null;
                $serverResponseMessage = $response_data['serverResponseMessage'] ?? null;
                $mnoTxnId = $response_data['mnoTxnId'] ?? null;
                $mnoResponseCode = $response_data['mnoResponseCode'] ?? null;
                $mnoResponseMessage = $response_data['mnoResponseMessage'] ?? null;

                $xml_response = [
                    'serverTxnId' => $serverTxnId,
                    'serverResponseCode' => $serverResponseCode,
                    'serverResponseMessage' => $serverResponseMessage,
                    'mnoTxnId' => $mnoTxnId,
                    'mnoResponseCode' => $mnoResponseCode,
                    'mnoResponseMessage' => $mnoResponseMessage
                ];

                return $xml_response;
            }
        } catch (Exception $e) {
            return "0160";
        }
    }
    // ******************************************************************
    // ******************Send Robi/Airtel SMS END************************
    // ******************************************************************



    /* send GP masking sms */
    // public static function update_send_masking_gp_sms($username, $password, $sender, $sms_text, $numbers)
    public static function update_send_masking_gp_sms($user_name, $password, $sms_text, $number, $sender, $billMsisdn, $apiKey, $msgType, $transType)
    {
        // if (self::is_unicode($sms_text)) {
        //     $sms_type = 3;
        // } else {
        //     $sms_type = 1;
        // }
        // foreach ($numbers as $key => $value) {
        //     if (empty($value)) {
        //         unset($numbers[$key]);
        //     }else{
        //         $numbers[$key] = substr($value, 2);
        //     }
        // }
        // $all_numbers_string = implode(',', $numbers);
        // $client = new Client();
        // $sms_text = urlencode($sms_text);
        // // dd($sms_text);
        // $url = "https://gpcmp.grameenphone.com/gpcmpapi/messageplatform/controller.home?username=$username&password=$password&apicode=6&msisdn=$all_numbers_string&countrycode=880&cli=$sender&messagetype=$sms_type&message=$sms_text&messageid=0";
        // // dd($url);
        

        // $res = $client->request('GET', $url);
        // // dump($res->getBody()->getContents());
        // // dd($url);
        
        // return $res->getBody()->getContents();

    }

    /*send robi/airtel masking sms*/
    public static function send_masking_mobireach_sms($user_name, $password, $sms_text, $number, $sender)
    {
        foreach ($number as $key => $value) {
            if (empty($value)) {
                unset($number[$key]);
            }
        }
        if (($user_name == '') || ($password == '') || ($sms_text == '') || empty($number) || ($sender == '')) {
            return "0150";/*something was missing*/
        } else {
            $client = new Client();
            $numbers = implode(',', $number);
            $sms_text = urlencode($sms_text);
            $url = "https://api.mobireach.com.bd/SendTextMultiMessage?Username=" . $user_name . "&Password=" . $password . "&From=" . $sender . "&To=" . $numbers . "&Message=" . $sms_text;
            // dd($url);
            try {
             try {
                $res = $client->request('GET', $url,[ 'headers' => [ 'User-Agent' => $_SERVER['HTTP_USER_AGENT'] ] ]);

             } catch(\GuzzleHttp\Exception\ClientException $e) {
                // return $e->getMessage();
                return "0160"; //Something went wrong to call api
                // $abc = new SimpleXMLElement($e->getResponse()->getBody()->getContents());
             }
            } catch(\Exception $e) {
                // return $e->getMessage();
               return "0160"; //Something went wrong to call api
            }
            try{
                $ret = $res->getBody()->getContents();
                 // dd($ret);
                $xml_response = simplexml_load_string($ret);
                // dd($xml_response);
                return $xml_response;
            }  catch(\Exception $e) {
                // return $e->getMessage();
                return "0160"; //Something went wrong to call api
            }

        }

    }


    public static function send_nonmasking_fusion_sms($user_name, $sms_text, $number, $sender)
    {
        foreach ($number as $key => $value) {
            if (empty($value)) {
                unset($number[$key]);
            }
        }
        if (($user_name == '') || ($sms_text == '') || empty($number) || ($sender == '')) {
            return "0150";/*something was missing*/
        } else {
            $client = new Client();
            $numbers = implode(',', $number);
            $number = substr($numbers,2);
            
            $sms_text = urlencode($sms_text);

            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //   CURLOPT_URL => 'http://api.fusionbd.net/API/SendSMS?username=' . $user_name . '&apiId=jo5Suw1f&json=True&destination=' . $number . '&source=' . $sender . '&text=' . $sms_text,
            //   CURLOPT_RETURNTRANSFER => true,
            //   CURLOPT_ENCODING => '',
            //   CURLOPT_MAXREDIRS => 10,
            //   CURLOPT_TIMEOUT => 0,
            //   CURLOPT_FOLLOWLOCATION => true,
            //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //   CURLOPT_CUSTOMREQUEST => 'POST',
            //   CURLOPT_HTTPHEADER => array(
            //     'Cookie: ASP.NET_SessionId=txsnyamvs3y1tul2gxmtwoya'
            //   ),
            // ));

            // $response = curl_exec($curl);
            // // $xml_response = json_decode($response,true);
            //     // dd($xml_response);
            // curl_close($curl);
                
            // return $response;

            // $response = curl_exec($curl);

            // curl_close($curl);
            // echo $response;
            
            $url = "http://api.fusionbd.net/API/SendSMS?username=" . $user_name  . "&apiId=jo5Suw1f&json=True&destination=" . $numbers . "&source=" . $sender . "&text=" . $sms_text;
            // dd($url);
            try {
             try {
                $res = $client->request('GET', $url,[ 'headers' => [ 'User-Agent' => $_SERVER['HTTP_USER_AGENT'] ] ]);

             } catch(\GuzzleHttp\Exception\ClientException $e) {
                // return $e->getMessage();
                return "0160"; //Something went wrong to call api
                // $abc = new SimpleXMLElement($e->getResponse()->getBody()->getContents());
             }
            } catch(\Exception $e) {
                // return $e->getMessage();
               return "0160"; //Something went wrong to call api
            }
            try{
                $ret = $res->getBody()->getContents();
                 // dd($ret);
                $xml_response = $ret;
                // dd($xml_response);
                return $xml_response;
            }  catch(\Exception $e) {
                // return $e->getMessage();
                return "0160"; //Something went wrong to call api
            }

        }

    }



    /*send banglalink masking sms*/
    public static function send_masking_banglalink_sms($user_name, $password, $sms_text, $number, $sender)
    {
        // dd($password);//return 'bl_error';
        try {
            foreach ($number as $key => $value) {
                if (empty($value)) {
                    unset($number[$key]);
                }
            }
            if (($user_name == '') || ($password == '') || ($sms_text == '') || empty($number) || ($sender == '')) {
                return "0150";/*something was missing*/
            } else {
                $client = new Client();
                $numbers = implode(',', $number);
                $sms_text = urlencode($sms_text);
                $url = "https://vas.banglalink.net/sendSMS/sendSMS?msisdn=" . $numbers . "&message=" . $sms_text . "&userID=" . $user_name . "&passwd=" . $password . "&sender=" . $sender;
                
                // dd($url);
                $res = $client->request('GET', $url);

                $ret = $res->getBody()->getContents();
    //            $xml_response = simplexml_load_string($ret);
                return $ret;
            }
        } catch (Exception $e){
            // dd($e);
            return "bl_error";//something went wrong
        }   
}



    /*send grameen phone masking sms*/
    public static function send_masking_gp_sms($user_name, $password, $sms_text, $number, $sender)
    {

        if (($user_name == '') || ($password == '') || ($sms_text == '') || empty($number) || ($sender == '')) {
            return "0150";/*something was missing*/
        } else {
            $client = new Client();
//            $sms_text = urlencode($sms_text);
            $apiCode = '1';
            $countryCode = '880';
            if (self::is_unicode($sms_text)) {
                $sms_type = 3;
                $message_gp = urlencode($sms_text);

            } else {
                $sms_type = 1;
                $message_gp = urlencode($sms_text);
            }

            foreach ($number as $key => $send_number) {
                if (empty($send_number)) {
                    unset($number[$key]);
                }else{
                    $gpNumbers = substr($send_number, 2);
                    $url = "https://gpcmp.grameenphone.com/gpcmpapi/messageplatform/controller.home?username=" . $user_name . "&password=" . $password . "&apicode=". $apiCode ."&msisdn=". $gpNumbers ."&countrycode=". $countryCode ."&cli=". $sender ."&messagetype=". $sms_type ."&message=". $message_gp ."&messageid=0";


                    $res = $client->request('GET', $url, ['verify' => false]);
                    $ret = $res->getBody()->getContents();
                }
            }
//            return $ret;
//            $xml_response = simplexml_load_string($ret);
            return $ret;
        }
    }


    /*send teletalk masking sms*/
    public static function send_masking_teletalk_sms($user_name, $password, $sms_text, $number, $sender)
    {

        if (($user_name == '') || ($password == '') || ($sms_text == '') || empty($number) || ($sender == '')) {
            return "0150";/*something was missing*/
        } else {
            try {
                $client = new Client();

                if (self::is_unicode($sms_text)) {
                    $sms_text = urlencode($sms_text);

                    if (empty($number)) {

                    } else {
                        $url = "http://bulksms.teletalk.com.bd/link_sms_send.php?op=SMS&user=" . $user_name . "&pass=" . $password . "&mobile=" . $number . "&charset=UTF-8&sms=" . $sms_text;

                        $res = $client->request('GET', $url);
                        $ret = $res->getBody()->getContents();
                    }


                } else {
                    $sms_text = urlencode($sms_text);

                    if (empty($number)) {

                    } else {
                        $url = "http://bulksms.teletalk.com.bd/link_sms_send.php?op=SMS&user=" . $user_name . "&pass=" . $password . "&mobile=" . $number . "&sms=" . $sms_text;

                        $res = $client->request('GET', $url);
                        $ret = $res->getBody()->getContents();
                    }

                }
                return $ret;
            }catch (Exception $e){
                return "0160";//something went wrong
            }
        }
    }



    /*non masking delivery report*/
    public static function getNonMaskingDeliveryReport($sms_ids){
        $authorization = base64_encode('IGLWEBLTD:igl2web1com');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.rankstelecom.com/sms/1/logs?messageid=".$sms_ids,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic $authorization"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        if ($err) {

            return $err;
        } else {
            //dd(json_decode($response));
            return $response;
        }
    }

    /*Get Browser Information*/
    public static function getBrowser() {
                  $u_agent = $_SERVER['HTTP_USER_AGENT'];
                  $bname = 'Unknown';
                  $platform = 'Unknown';
                  $version= "";
                  // First get the platform?
                  if (preg_match('/linux/i', $u_agent)) {
                    $platform = 'linux';
                  } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
                    $platform = 'mac';
                  } elseif (preg_match('/windows|win32/i', $u_agent)) {
                    $platform = 'windows';
                  }
                  // Next get the name of the useragent yes seperately and for good reason
                  if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
                    $bname = 'Internet Explorer';
                    $ub = "MSIE";
                  } elseif(preg_match('/Firefox/i',$u_agent)) {
                    $bname = 'Mozilla Firefox';
                    $ub = "Firefox";
                  } elseif(preg_match('/Chrome/i',$u_agent)) {
                    $bname = 'Google Chrome';
                    $ub = "Chrome";
                  } elseif(preg_match('/Safari/i',$u_agent)) {
                    $bname = 'Apple Safari';
                    $ub = "Safari";
                  } elseif(preg_match('/Opera/i',$u_agent)) {
                    $bname = 'Opera';
                    $ub = "Opera";
                  } elseif(preg_match('/Netscape/i',$u_agent)) {
                    $bname = 'Netscape';
                    $ub = "Netscape";
                  }
                  // finally get the correct version number
                  $known = array('Version', $ub, 'other');
                  $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
                  if (!preg_match_all($pattern, $u_agent, $matches)) {
                    // we have no matching number just continue
                  }
                  // see how many we have
                  $i = count($matches['browser']);
                  if ($i != 1) {
                    //we will have two since we are not using 'other' argument yet
                    //see if version is before or after the name
                    if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                      $version= $matches['version'][0];
                    } else {
                      $version= $matches['version'][1];
                    }
                  } else {
                    $version= $matches['version'][0];
                  }
                  // check if we have a number
                  if ($version==null || $version=="") {$version="?";}
                return array(
                  'userAgent' => $u_agent,
                  'name'      => $bname,
                  'version'   => $version,
                  'platform'  => $platform,
                  'pattern'    => $pattern
                  );
                }

            public static function os_info($uagent)
                {
                    
                    global $uagent;
                    $oses   = array(
                        'Win311' => 'Win16',
                        'Win95' => '(Windows 95)|(Win95)|(Windows_95)',
                        'WinME' => '(Windows 98)|(Win 9x 4.90)|(Windows ME)',
                        'Win98' => '(Windows 98)|(Win98)',
                        'Win2000' => '(Windows NT 5.0)|(Windows 2000)',
                        'WinXP' => '(Windows NT 5.1)|(Windows XP)',
                        'WinServer2003' => '(Windows NT 5.2)',
                        'WinVista' => '(Windows NT 6.0)',
                        'Windows7' => '(Windows NT 6.1)',
                        'Windows8' => '(Windows NT 6.2)',
                        'WinNT' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
                        'OpenBSD' => 'OpenBSD',
                        'SunOS' => 'SunOS',
                        'Ubuntu' => 'Ubuntu',
                        'Android' => 'Android',
                        'Linux' => '(Linux)|(X11)',
                        'iPhone' => 'iPhone',
                        'iPad' => 'iPad',
                        'MacOS' => '(Mac_PowerPC)|(Macintosh)',
                        'QNX' => 'QNX',
                        'BeOS' => 'BeOS',
                        'OS2' => 'OS/2',
                        'SearchBot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
                    );
                    $uagent = strtolower($uagent ? $uagent : $_SERVER['HTTP_USER_AGENT']);
                    foreach ($oses as $os => $pattern)
                        if (preg_match('/' . $pattern . '/i', $uagent))
                            return $os;
                    return 'Unknown';
                }
// Flexiload to a number .... 
    public static  function sendFlexiload($url, $post){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        $result = curl_exec($ch);
        curl_close($ch);  // Seems like good practice
        // dump($result);
        return $result;

    }

    public static function send_desktop_sms($userName,$password,$number,$sms_text)
    {
        // return 'bl_error';
        // dd($sms_text);
        try {
            foreach ($number as $key => $value) {
                if (empty($value)) {
                    unset($number[$key]);
                }
            }
            
            if (empty($userName) || ($password == '') || ($sms_text == '') || empty($number)) {
                return "0150";/*something was missing*/
            } else {
                // $client = new Client();
                $numbers = implode(',', $number);
                // // dd($numbers);
                $text = str_replace('"',"",$sms_text);
                // // dd($uniqueids);
                $targeted_number = \PhoneNumber::addNumberPrefix($numbers);
                // if (self::is_unicode($sms_text)) {
                //     $type = 0;
                // }else{
                //     $type = 0;
                // }

                // $sms_text = urlencode($sms_text);
                // // dd($numbers);

                // $url = "http://209.126.78.29:20003/sendsms?account=" . $userName . "&password=" . $password . "&smstype=" . $type . "&numbers=" . $targeted_number . "&content=". $sms_text;
                
                
                // // dd($url);
                // $res = $client->request('GET', $url);
                // // dd($res);
                // $ret = $res->getBody()->getContents();
                
                // $xml_response = $ret;

                // return json_decode($xml_response);
                

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://174.138.172.82:20003/sendsms',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "account":"' . $userName . '",
                "password":"' . $password . '",
                "smstype":0,
                "numbers":"' . $targeted_number . '",
                "content":"' . $text . '",
                "mmstitle=mmstitle_text"
                }',
                CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
                ),
                ));


                $response = curl_exec($curl);
                $xml_response = json_decode($response);
                // dd($xml_response);
                curl_close($curl);
                
                return $xml_response;
            }
        } catch (Exception $e){
            // dd($e);
            $xml_response = "blast";
            return $xml_response;//something went wrong
        }   
    }


    public static function send_desktop_single_sms($userName,$password,$number,$sms_text)
    {
        //return 'bl_error';
        // dd($userName);
        try {
            foreach ($number as $key => $value) {
                if (empty($value)) {
                    unset($number[$key]);
                }
            }
            
            if (empty($userName) || ($password == '') || ($sms_text == '') || empty($number)) {
                return "0150";/*something was missing*/
            } else {
                // $client = new Client();
                $numbers = implode(',', $number);
                // // dd($numbers);
                // // $sms_text = urlencode($sms_text);
                // // dd($uniqueids);
                $targeted_number = \PhoneNumber::addNumberPrefix($numbers);
                // if (self::is_unicode($sms_text)) {
                //     $type = 0;
                // }else{
                //     $type = 0;
                // }

                // $sms_text = urlencode($sms_text);
                // // dd($numbers);

                // $url = "http://209.126.78.29:20003/sendsms?account=" . $userName . "&password=" . $password . "&smstype=" . $type . "&numbers=" . $targeted_number . "&content=". $sms_text;
                
                
                // // dd($url);
                // $res = $client->request('GET', $url);
                // // dd($res);
                // $ret = $res->getBody()->getContents();
                
                // $xml_response = $ret;

                // return json_decode($xml_response);
                

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://174.138.172.82:20003/sendsms',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
                "account":"' . $userName . '",
                "password":"' . $password . '",
                "type":0,
                "numbers":"' . $targeted_number . '",
                "content":"' . $sms_text . '"
                }',
                CURLOPT_HTTPHEADER => array(
                'Content-Type: text/plain'
                ),
                ));

                $response = curl_exec($curl);
                $xml_response = json_decode($response);
                dd($xml_response);
                curl_close($curl);
                return $xml_response;
            }
        } catch (Exception $e){
            // dd($e);
            $xml_response = "blast";
            return $xml_response;//something went wrong
        }   
    }

    public static function getRoute2DeliveryReport($sms_ids,$user_name,$password){
        // dd($sms_ids);


        //$authorization = base64_encode('IGLWEBLTD:igl2web1com');


        $smsIds = implode(',',$sms_ids);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://209.126.78.29:20003/getreport?account=" . $user_name . "&password=" . $password . "&ids=". $smsIds,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        if ($err) {

            return $err;
        } else {
            $result = json_decode($response);
            // dd($result->array);
            
                return $result;
            
            // if ($result->array[0][5] != '') {
            //     return $result;
            // }else{
            //     $result = 'error';
            //     return $result;
            // }
            
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://174.138.172.82:20003/getreport?account='. $user_name .'&password=' . $password . '&ids=' . $smsIds,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        dd($response);
        curl_close($curl);
        $result = json_decode($response);
        return $result;
    }


}
