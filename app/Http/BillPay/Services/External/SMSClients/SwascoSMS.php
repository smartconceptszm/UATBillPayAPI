<?php

namespace App\Http\BillPay\Services\External\SMSClients;

use App\Http\BillPay\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;

class SwascoSMS implements ISMSClient
{

    private $sms_SENDER_ID;
    private $sms_baseURL;
    private $sms_APIKEY;

    public function __construct(string $sms_baseURL,string $sms_APIKEY, string $sms_SENDER_ID)
    {
        $this->sms_SENDER_ID = $sms_SENDER_ID;
        $this->sms_baseURL = $sms_baseURL;
        $this->sms_APIKEY = $sms_APIKEY;
    }

    public function channelChargeable():bool
    {
        return false;
    }

    public function send(array $smsParams): bool
    {

        $smsSent = false;
        try {
            if(\substr($smsParams['mobileNumber'],0,1)== "+"){
                $smsParams['mobileNumber'] = \substr($smsParams['mobileNumber'],1,\strlen($smsParams['mobileNumber'])-1);
            }            
            $smsParams['message'] = \str_replace("/C", "cc", $smsParams['message']);
            $smsParams['message'] = \str_replace(\chr(47), "", $smsParams['message']);

            $fullURL = $this->sms_baseURL.$this->sms_APIKEY."/contacts/".$smsParams['mobileNumber']. 
                        "/senderId/".$this->sms_SENDER_ID."/message/".\rawurlencode($smsParams['message']);
            
            $apiResponse = Http::timeout($this->swascoTimeout)->withHeaders([
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
