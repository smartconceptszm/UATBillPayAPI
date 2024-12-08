<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Clients\SMSChannelCredentialsService;
use App\Http\Services\Clients\SMSProviderCredentialService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MTNSMS implements ISMSClient
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
            $apiToken = $this->getToken($credentials);
            if(!$smsParams['transactionId']){
                $smsParams['transactionId'] = $smsParams['mobileNumber']."T".\date('ymdHis');
            }
            $fullURL = $credentials['SMS_GATEWAY_URL'].'/sms/send';
            $messageBody = [
                                "name" => "Non-Bulk campaign",
                                "msg" => $smsParams['message'],
                                "recipient"=>$smsParams['mobileNumber'],
                                "sender" => $credentials['SMS_SENDER_ID'],
                                "category" => $credentials['SMS_CATEGORY'],     // OTP|TXN|Promo
                                "clientTxnId" => $smsParams['transactionId'],
                                "country" => "ZM"
                        ];
                        
            $apiResponse = Http::timeout($credentials['SMS_GATEWAY_Timeout'])
                                 ->withHeaders([
                                        'Content-Type' => 'application/json',
                                       'Accept' => '*/*'
                                    ])
                                ->withToken($apiToken['access_token'])
                                ->withOptions([
                                            'verify' => false,  // Disable SSL verification
                                        ])
                                ->post($fullURL,$messageBody);
            if ($apiResponse->status()>=200 && $apiResponse->status()<300 ) {
                $apiResponse=$apiResponse->json();
                if($apiResponse['statusCode'] == 0){
                    $response = true;
                }else{
                    throw new \Exception('SMS Not sent by MTN. Server responded with'.$apiResponse['statusMsg']);
                }
            }else{
                throw new \Exception('SMS Not sent by MTN. Server responded with Status Code'.$apiResponse->status());
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            $response = false;
        }
        return $response;

    }


    private function getToken(array $configs): Array
    {
 
       $response=['access_token'=>''];
 
       try {
          $fullURL = $configs['SMS_GATEWAY_URL'].'/accounts/users/login';
          $apiResponse = Http::timeout($configs['SMS_GATEWAY_Timeout'])
                                ->withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Accept' => '*/*'
                                  ])
                                ->withOptions([
                                            'verify' => false,  // Disable SSL verification
                                    ])
                                ->post($fullURL,
                                        ['email'=>$configs['EMAIL'],'password'=>$configs['PASSWORD']]
                                    );

          if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
                $apiResponse=$apiResponse->json();
                $response['access_token']=$apiResponse['access_token'];
          } else{
             throw new \Exception("MTNSMS API Get Token error. MTN SMS responded with status code: ".$apiResponse->status().".", 1);
          }
       } catch (\Throwable $e) {
          if ($e->getCode()==1) {
             throw new \Exception($e->getMessage(), 1);
          } else {
             throw new \Exception("MTNSMS API Get Token error. Details: ".$e->getMessage(), 1);
          }
       }
       return $response;
    }

    private function getConfigs(array $smsParams):array
    {
 
        $channelCredentials = $this->channelCredentialsService->getSMSChannelCredentials($smsParams['channel_id']);
        $smsProviderCredentials = $this->smsProviderCredentialService->getSMSProviderCredentials($smsParams['sms_provider_id']);
        $configs['SMS_GATEWAY_Timeout'] = $smsProviderCredentials['MTN_SMS_GATEWAY_Timeout'];
        $configs['SMS_GATEWAY_URL'] = $smsProviderCredentials['MTN_SMS_GATEWAY_URL'];
        $configs['SMS_CATEGORY'] = $smsProviderCredentials['MTN_SMS_CATEGORY'];

        $configs['SMS_SENDER_ID'] = $channelCredentials['MTN_SMS_SENDER_ID'];
        $configs['PASSWORD'] = $channelCredentials['MTN_PASSWORD'];
        $configs['EMAIL'] = $channelCredentials['MTN_EMAIL'];
        return $configs;
 
    }


}