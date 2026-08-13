<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Clients\SMSChannelCredentialsService;
use App\Http\Services\Clients\SMSProviderCredentialService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AirtelSMS implements ISMSClient
{

    public function __construct(
        private SMSChannelCredentialsService $channelCredentialsService,
        private SMSProviderCredentialService $smsProviderCredentialService)
     {}


    /**
     * Send sms message.
     *
     * @param  array  $smsParams['mobileNumber'=>'','message'=>'','channel'=>'']
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
            $smsParams['message'] =  \str_replace(\chr(47), "", $smsParams['message']);
            $smsParams['message'] =  str_replace(["\n", "\r", "\r\n"], " ", $smsParams['message']); 
            // $smsParams['message'] = urlencode($smsParams['message']);

            $fullURL = $credentials['SMS_GATEWAY_URL']."REQUESTTYPE=SMSSubmitReq&MOBILENO=".$smsParams['mobileNumber'].
                        "&USERNAME=".$credentials['SMS_GATEWAY_USERNAME']."&ORIGIN_ADDR=".$credentials['SMS_SENDER_ID']. 
                        "&TYPE=0&MESSAGE=".\rawurlencode($smsParams['message'])."&PASSWORD=".$credentials['SMS_GATEWAY_PASSWORD'];
                   
            $apiResponse = Http::timeout($credentials['SMS_GATEWAY_Timeout'])
                                 ->withHeaders([
                                       'Accept' => '*/*'
                                    ])->get($fullURL);
            if ($apiResponse->status()>=200 && $apiResponse->status()<300 ) {
                $apiResponse=$apiResponse->body();
                $responseArr = explode('|', $apiResponse);
                if($responseArr[0] == '+OK'){
                    $response = true;
                }else{
                    throw new Exception($responseArr[2], 1);
                }
            }else{
                Log::error('SMS Not sent by AIRTEL. Server responded with Status Code'.$apiResponse->status());
            }
        } catch (\Throwable $e) {
            Log::error('SMS Not sent by AIRTEL. Details: '.$e->getMessage());
            $response = false;
        }
        return $response;

    }

    private function getConfigs(array $smsParams):array
    {
 
        $channelCredentials = $this->channelCredentialsService->getSMSChannelCredentials($smsParams['channel_id']);
        $smsProviderCredentials = $this->smsProviderCredentialService->getSMSProviderCredentials($smsParams['sms_provider_id']);

        $configs['SMS_GATEWAY_Timeout'] = $smsProviderCredentials['AIRTEL_SMS_GATEWAY_Timeout'];
        $configs['SMS_GATEWAY_URL'] = $smsProviderCredentials['AIRTEL_SMS_GATEWAY_URL'];
        
        $configs['SMS_GATEWAY_USERNAME'] = $channelCredentials['AIRTEL_SMS_GATEWAY_USERNAME'];
        $configs['SMS_GATEWAY_PASSWORD'] = $channelCredentials['AIRTEL_SMS_GATEWAY_PASSWORD'];
        $configs['SMS_SENDER_ID'] = $channelCredentials['AIRTEL_SMS_SENDER_ID'];
        return $configs;
 
    }

}