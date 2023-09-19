<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZamtelSMS implements ISMSClient
{


    public function channelChargeable():bool
    {
        return true;
    }

    /**
     * Send sms message.
     *
     * @param  Array  $smsParams['mobileNumber'=>'','message'=>'','clientShortName'=>'']
     * @return Bool 
     */
    public function send(Array $smsParams): bool
    {

        $response = false;
        try {
            
            
            $sms_Timeout = \env('SMS_GATEWAY_Timeout'); 
            $sms_APIKEY = \env('SMS_GATEWAY_APIKEY');
            $sms_baseURL = \env('SMS_GATEWAY_URL');
            if(\substr($smsParams['mobileNumber'],0,1)== "+"){
                $smsParams['mobileNumber'] = \substr($smsParams['mobileNumber'],1,\strlen($smsParams['mobileNumber'])-1);
            } 
            $smsParams['message'] = \str_replace(\chr(47), "", $smsParams['message']);
            $fullURL = $sms_baseURL.$sms_APIKEY."/contacts/".$smsParams['mobileNumber']. 
                        "/senderId/".$smsParams['clientShortName']."/message/".\rawurlencode($smsParams['message']);
            $apiResponse = Http::timeout($sms_Timeout)
                                 ->withHeaders([
                                       'Accept' => '*/*'
                                    ])->get($fullURL);
            if ($apiResponse->status()>=200 && $apiResponse->status()<300 ) {
                $response = true;
            }else{
                Log::error('SMS Not sent by Zamtel. Server responded with Status Code'.$apiResponse->status());
            }
        } catch (\Throwable $e) {
            Log::error('SMS Not sent by Zamtel. Details: '.$e->getMessage());
            $response = false;
        }
        return $response;

    }

}