<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Clients\SMSChannelCredentialsService;
use App\Http\Services\Clients\SMSProviderCredentialService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiafaanSMS implements ISMSClient
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

            if(strlen($smsParams['mobileNumber'])== 12){
                $smsParams['mobileNumber'] = "+".$smsParams['mobileNumber'];
            } 
            $smsParams['message'] = \str_replace(\chr(47), "", $smsParams['message']);

            //

            $fullURL = $credentials['SMS_GATEWAY_URL'].
                        "?username=".urlencode($credentials['SMS_GATEWAY_USERNAME']). 
                        "&password=".urlencode($credentials['SMS_GATEWAY_PASSWORD']).
                        "&messagetype=".urlencode($credentials['SMS_GATEWAY_MESSAGE_TYPE']).
                        "&to=".urlencode($smsParams['mobileNumber']).
                        "&message=".urlencode($smsParams['message']);
            $apiResponse = Http::timeout($credentials['SMS_GATEWAY_Timeout'])
                                 ->withHeaders([
                                       'Accept' => '*/*'
                                    ])->get($fullURL);
            if ($apiResponse->status()>=200 && $apiResponse->status()<300 ) {
                $response = true;
            }else{
                Log::error('SMS Not sent by Diafaan. Server responded with Status Code'.$apiResponse->status());
            }
            
        } catch (\Throwable $e) {
            Log::error('SMS Not sent by Diafaan. Details: '.$e->getMessage());
            $response = false;
        }
        return  $response;

    }

    private function getConfigs(array $smsParams):array
    {
 
        $channelCredentials = $this->channelCredentialsService->getSMSChannelCredentials($smsParams['channel_id']);
        $smsProviderCredentials = $this->smsProviderCredentialService->getSMSProviderCredentials($smsParams['sms_provider_id']);

        $configs['SMS_GATEWAY_Timeout'] = $smsProviderCredentials['DIAFAAN_SMS_GATEWAY_Timeout'];
        $configs['SMS_GATEWAY_URL'] = $smsProviderCredentials['DIAFAAN_SMS_GATEWAY_URL'];
        
        $configs['SMS_GATEWAY_MESSAGE_TYPE'] = $channelCredentials['DIAFAAN_SMS_GATEWAY_MESSAGE_TYPE'];
        $configs['SMS_GATEWAY_USERNAME'] = $channelCredentials['DIAFAAN_SMS_GATEWAY_USERNAME'];
        $configs['SMS_GATEWAY_PASSWORD'] = $channelCredentials['DIAFAAN_SMS_GATEWAY_PASSWORD'];
        return $configs;
 
    }

}