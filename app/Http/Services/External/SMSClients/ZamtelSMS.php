<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Clients\SMSChannelCredentialsService;
use App\Http\Services\Clients\SMSProviderCredentialService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZamtelSMS implements ISMSClient
{

    public function __construct(
        private SMSChannelCredentialsService $channelCredentialsService,
        private SMSProviderCredentialService $smsProviderCredentialService)
     {}


    /**
     * Send sms message.
     *
     * @param  Array  $smsParams['mobileNumber'=>'','message'=>'','channel'=>'']
     * @return Bool 
     */
    public function send(Array $smsParams): bool
    {

        $response = false;
        try {
            
            $credentials = $this->getConfigs($smsParams);

            if(\substr($smsParams['mobileNumber'],0,1)== "+"){
                $smsParams['mobileNumber'] = \substr($smsParams['mobileNumber'],1,\strlen($smsParams['mobileNumber'])-1);
            } 
            $smsParams['message'] = \str_replace(\chr(47), "", $smsParams['message']);
            $fullURL = $credentials['SMS_GATEWAY_URL'].$credentials['SMS_GATEWAY_APIKEY']."/contacts/".$smsParams['mobileNumber']. 
                        "/senderId/".$credentials['SMS_SENDER_ID']."/message/".\rawurlencode($smsParams['message']);
            $apiResponse = Http::timeout($credentials['SMS_GATEWAY_Timeout'])
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

    private function getConfigs(array $smsParams):array
    {
 
        $channelCredentials = $this->channelCredentialsService->getSMSChannelCredentials($smsParams['channel_id']);
        $smsProviderCredentials = $this->smsProviderCredentialService->getSMSProviderCredentials($smsParams['sms_provider_id']);

        $configs['SMS_GATEWAY_Timeout'] = $smsProviderCredentials['ZAMTEL_SMS_GATEWAY_Timeout'];
        $configs['SMS_GATEWAY_URL'] = $smsProviderCredentials['ZAMTEL_SMS_GATEWAY_URL'];
        
        $configs['SMS_GATEWAY_APIKEY'] = $channelCredentials['ZAMTEL_SMS_GATEWAY_APIKEY'];
        $configs['SMS_SENDER_ID'] = $channelCredentials['ZAMTEL_SMS_SENDER_ID'];
        return $configs;
 
    }

}