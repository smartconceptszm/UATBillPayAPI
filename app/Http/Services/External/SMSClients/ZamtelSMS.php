<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\Web\Clients\ClientMnoCredentialsService;
use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZamtelSMS implements ISMSClient
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

            if(\substr($smsParams['mobileNumber'],0,1)== "+"){
                $smsParams['mobileNumber'] = \substr($smsParams['mobileNumber'],1,\strlen($smsParams['mobileNumber'])-1);
            } 
            $smsParams['message'] = \str_replace(\chr(47), "", $smsParams['message']);
            $fullURL = $credentials['API_KEY'].$credentials['API_KEY']."/contacts/".$smsParams['mobileNumber']. 
                        "/senderId/".$credentials['SENDER_ID']."/message/".\rawurlencode($smsParams['message']);
            $apiResponse = Http::timeout($credentials['Timeout'])
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