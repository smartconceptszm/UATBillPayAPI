<?php

namespace App\Http\Services\External\SMSClients;

use App\Http\Services\External\SMSClients\ISMSClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MTNMoMoDeliverySMS implements ISMSClient
{

   public function channelChargeable():bool
   {
       return false;
   }

   public function send(array $mtnParams): bool{
      $response = false;
      try {
         $configs = $this->getConfigs($mtnParams['urlPrefix']);
         $apiToken = $this->getToken($configs);
         $token=$apiToken['access_token'];
         $fullURL = $configs['baseURL']."v1_0/requesttopay/".$mtnParams['transactionId']."/deliverynotification";
         $response = Http::timeout($configs['timeout'])
               ->withHeaders([
                        'X-Target-Environment'=>$configs['targetEnv'],
                        'Content-Type' => 'application/json',
                        'Ocp-Apim-Subscription-Key' => $configs['clientId']
                  ])
               ->withToken($token)
               ->post($fullURL, [
                     'notificationMessage'=>\substr(\str_replace("\n", " ", $mtnParams['message']),0,159)
                  ]);
         if($response->status()>=200 && $response->status()<300 ){
            $response = true;
         }else{
               throw new Exception("Error on deliver notification. MTN MoMo responded with status code: ".$response->status(), 2);
         }
      } catch (\Throwable $e) {
         Log::error('SMS Not sent by MTN MoMo. Details: '.$e->getMessage());
         $response = false;
      }
      return $response;
   }

   private function getToken(array $configs): Array
   {

      $response=['access_token'=>'',
                     'expires_in'=>''];

      try {
         $fullURL = $configs['baseURL']."token/";
         $apiResponse = Http::timeout($configs['timeout'])
               ->withBasicAuth($configs['clientUserName'], $configs['clientPassword'])
               ->withHeaders([
                  'Ocp-Apim-Subscription-Key' => $configs['clientId'],
                  'Accept' => '*/*'
               ])->post($fullURL,[]);
         
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
               $apiResponse=$apiResponse->json();
               $response['access_token']=$apiResponse['access_token'];
               $response['expires_in']=$apiResponse['expires_in'];
         } else{
               throw new Exception("MTN API Get Token error. MTN MoMo responded with status code: ".$apiResponse->status().".", 1);
         }
      } catch (\Throwable $e) {
         if ($e->getCode()==1) {
               throw new Exception($e->getMessage(), 1);
         } else {
               throw new Exception("MTN API Get Token error. Details: ".$e->getMessage(), 1);
         }
      }
      return $response;
   }

   private function getConfigs(string $urlPrefix):array
   {
      return [
               'clientUserName'=>\env(\strtoupper($urlPrefix).'_MTN_USERNAME'),
               'clientPassword'=>\env(\strtoupper($urlPrefix).'_MTN_PASSWORD'),
               'clientId'=>\env(\strtoupper($urlPrefix).'_MTN_OCPKEY'),
               'targetEnv'=>\env('MTN_TARGET_ENVIRONMENT'),
               'timeout'=>\env('MTN_Http_Timeout'),
               'txCurrency'=>\env('MTN_CURRENCY'),
               'baseURL'=>\env('MTN_BASE_URL')

         ];
   }

}