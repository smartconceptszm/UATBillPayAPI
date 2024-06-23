<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;

class SwascoSMS implements ISMSClient
{

    public function __construct(
        private BillingCredentialService $billingCredentialService) 
    {}

    public function channelChargeable():bool
    {
        return false;
    }

    public function send(array $smsParams): bool
    {

        $smsSent = false;
        try {
            $billingCredentials = $this->billingCredentialService->getClientCredentials($smsParams['client_id']);
            $sms_SENDER_ID = $billingCredentials['SMS_SENDER_ID'];
            $sms_baseURL = $billingCredentials['SMS_BASE_URL'];
            $sms_APIKEY = $billingCredentials['SMS_APIKEY'];
            $swascoTimeout = $billingCredentials['REMOTE_TIMEOUT'];
            
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
