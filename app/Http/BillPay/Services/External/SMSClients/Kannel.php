<?php

namespace App\Http\BillPay\Services\External\SMSClients;

use App\Http\BillPay\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Kannel implements ISMSClient
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
            $baseURL = \env("KANNEL_BASE_URL");
            $smsSendPort = \env("KANNEL_SMSSEND_PORT");
            $username = \env("KANNEL_USERNAME");
            $password = \env("KANNEL_PASSWORD");
            $fullURL = $baseURL.":".$smsSendPort."/cgi-bin/sendsms?username=".
                        $username."&password=".$password."&to=".$smsParams['mobileNumber'].
                        "&text=".\rawurlencode($smsParams['message'])."&from=info";
            $apiResponse = Http::withHeaders([
                            "Accept" => "*/*",
                        ])->get($fullURL);
            if ($apiResponse->status() >= 200 && $apiResponse->status() < 300) {
                $response = true;
            } 
        } catch (\Throwable $e) {
            Log::error('SMS not sent by Kannel. Details: '.$e->getMessage());
            $response = false;
        }
        return $response;

    }

    public function getKannelStatus(): array
    {

        $response = [
                "status" => "DOWN",
                "message"=>"",
            ];
        try {
            $baseURL = \env("KANNEL_BASE_URL");
            $smppStatusPort = \env("KANNEL_SMPPSTATUS_PORT");
            $fullURL = $baseURL.":".$smppStatusPort."/status";
            $apiResponse = Http::withHeaders([
                "Accept" => "*/*",
            ])->get($fullURL);
            if ($apiResponse->status() >= 200 && $apiResponse->status() < 300) {
                $response['status'] = "UP";
                $response['message'] = $apiResponse->getBody()->getContents();
            } else {
                $response['message'] = $apiResponse->getBody()->getContents();
            }
        } catch (\Throwable $e) {
            $response['message'] = "Kannel get status error. Details: " . $e->getMessage();
        }
        return $response;
        
    }

}