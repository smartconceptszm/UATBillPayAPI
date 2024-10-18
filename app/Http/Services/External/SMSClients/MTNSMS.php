<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Clients\ClientMnoCredentialsService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MTNSMS implements ISMSClient
{

    public function __construct(
        private ClientMnoCredentialsService $channelCredentialsService)
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
            
            $credentials = $this->channelCredentialsService->getSMSCredentials($smsParams['channel_id']);
            $apiToken = $this->getToken($credentials);
            if(!$smsParams['transaction_id']){
                $smsParams['transaction_id'] = $smsParams['mobileNumber']."_".\date('ymdHis');
            }
            $fullURL = $credentials['SMS_GATEWAY_URL'].'/sms/send';
            $messageBody = [
                                "name" => "Non-Bulk campaign",
                                "msg" => $smsParams['message'],
                                "recipient"=>$smsParams['mobileNumber'],
                                "sender" => $credentials['SMS_SENDER_ID'],
                                "category" => $credentials['SMS_CATEGORY'],     // OTP|TXN|Promo
                                "clientTxnId" => $smsParams['transaction_id'],
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
                               ->post($fullURL,['email'=>$configs['EMAIL'],'password'=>$configs['PASSWORD']]);
          
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

}