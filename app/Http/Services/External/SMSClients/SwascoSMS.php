<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;

class SwascoSMS implements ISMSClient
{

    public function channelChargeable():bool
    {
        return false;
    }

    public function send(array $smsParams): bool
    {

        $smsSent = false;
        try {

            $sms_SENDER_ID = \env('SWASCO_SMS_SENDER_ID');
            $sms_baseURL = \env('SWASCO_SMS_BASE_URL');
            $sms_APIKEY = \env('SWASCO_SMS_APIKEY');
            $swascoTimeout = \env('SWASCO_REMOTE_TIMEOUT');
            
            if(\substr($smsParams['mobileNumber'],0,1)== "+"){
                $smsParams['mobileNumber'] = \substr($smsParams['mobileNumber'],1,\strlen($smsParams['mobileNumber'])-1);
            }            
            $smsParams['message'] = \str_replace("/C", "cc", $smsParams['message']);
            $smsParams['message'] = \str_replace(\chr(47), "", $smsParams['message']);

            $fullURL = $sms_baseURL.$sms_APIKEY."/contacts/".$smsParams['mobileNumber']. 
                        "/senderId/".$sms_SENDER_ID."/message/".\rawurlencode($smsParams['message']);
            
            $apiResponse = Http::timeout($swascoTimeout)->withHeaders([
                    'Accept' => '*/*'
                ])->get($fullURL);

            if ($apiResponse->status()>=200 && $apiResponse->status()<300 ) {
                $smsSent = true;
            }

        } catch (\Throwable $e) {
            $smsSent = false;
        }
        return $smsSent;
        
    }

}
