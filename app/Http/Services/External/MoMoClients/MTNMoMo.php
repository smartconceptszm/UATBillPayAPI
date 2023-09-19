<?php

namespace App\Http\Services\External\MoMoClients;

use App\Http\Services\External\MoMoClients\IMoMoClient;
use Illuminate\Support\Facades\Http;
use App\Http\DTOs\BaseDTO;
use Illuminate\Support\Str;
use Exception;

class MTNMoMo implements IMoMoClient
{
    
   public function requestPayment(object $dto):object
   {

      $mainResponse=[
         'status' => 'SUBMISSION FAILED',
         'transactionId' => '',
         'error'=>'',
      ];

      try {
         $mainResponse['transactionId'] = (string)Str::uuid();
         $configs = $this->getConfigs($dto->urlPrefix);
         $apiToken = $this->getToken($configs);
         $token=$apiToken['access_token'];

         $fullURL = $configs['baseURL']."v1_0/requesttopay";
         $mtnResponse = Http::timeout($configs['timeout'])->withHeaders([
                           'X-Reference-Id'=>$mainResponse['transactionId'],
                           'X-Target-Environment'=>$configs['targetEnv'],
                           'Content-Type' => 'application/json',
                           'Ocp-Apim-Subscription-Key' => $configs['clientId'],
                           'Accept' => '*/*',
                     ])
               ->withToken($token)
               ->post($fullURL, [
                  "amount"=> $dto->paymentAmount,
                  'currency'=>$configs['txCurrency'],
                  "externalId"=>$mainResponse['transactionId'],
                  "payer" => [
                     "partyIdType"=>"MSISDN",
                     "partyId"=>$dto->mobileNumber, 
                  ],
                  "payerMessage"=>"Payment Request",
                  "payeeNote"=>"Click yes to approve"
         ]);

         if($mtnResponse->status()>=200 && $mtnResponse->status()<300 ){
               $mainResponse['status']="SUBMITTED";
         }else{
               throw new Exception("Error on collect funds. MTN MoMo responded with status code: ".$mtnResponse->status().".", 2);
         }
      } catch (\Throwable $e) {
         if($e->getCode()==1 || $e->getCode()==2){
               $mainResponse['error']=$e->getMessage();
         }else{
               $mainResponse['error']="Error on collect funds. MTN MoMo details: ".$e->getMessage();
         }
      }
      return (object)$mainResponse;
      
   }

   public function confirmPayment(BaseDTO $dto): BaseDTO
   {

      $response=['status'=>"FAILED",
                  'mnoTransactionId'=>'',
                  'error'=>''];

      try {
         $configs = $this->getConfigs($dto->urlPrefix);
         $apiToken = $this->getToken($configs);
         $token=$apiToken['access_token'];
         $fullURL = $configs['baseURL']."v1_0/requesttopay/".$dto->transactionId;
         $apiResponse = Http::timeout($configs['timeout'])->withHeaders([
                              'Ocp-Apim-Subscription-Key' => $configs['clientId'],
                              'X-Target-Environment'=>$configs['targetEnv'],
                              'Content-Type' => 'application/json',
                              'Accept' => '*/*'   
                           ])
                     ->withToken($token)
                     ->get($fullURL);
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
               $apiResponse=$apiResponse->json();
               if($apiResponse['status']==='SUCCESSFUL'){
                  $response['status'] = "PAID";
                  $response['mnoTransactionId']=$apiResponse['financialTransactionId'];
               }else{
                  switch ($apiResponse['status']) {
                     case 'FAILED':
                           if(is_array($apiResponse['reason'])){
                              if(\array_key_exists('message', $apiResponse['reason'])){
                                 throw new Exception("MTN response: ". $apiResponse['reason']['message'].".", 2);
                              }
                           }
                           throw new Exception("MTN response: ". $apiResponse['reason'].".", 2);
                           break;
                     case 'PENDING':
                           throw new Exception("Error on get transaction status. MTN response: ". $apiResponse['status'].".", 2);
                           break;
                     default:
                           throw new Exception("Error on get transaction status. MTN response: ". $apiResponse['status'].".", 2);
                           break;
                  }
               }
         }else{
               switch ($apiResponse->status()) {
                  case 400:
                     throw new Exception("Error on get transaction status. MTN response: Request not properly formatted.", 2);
                     break;
                  case 404:
                     throw new Exception("MTN response: Requested resource was not found.", 2);
                     break;    
                  case 500:
                     throw new Exception("Error on get transaction status. MTN response: An internal error occurred while processing.", 2);
                     break;                  
                  default:
                     throw new Exception("Error on get transaction status. MTN response: Status Code ".$apiResponse->status().".", 2);
                     break;
               }
         }
      } catch (\Throwable $e) {
         $response['status'] = "FAILED";
         if ($e->getCode()==2 || $e->getCode()==3) {
               $response['error']=$e->getMessage();
         } else {
               $response['error']="Error on get transaction status. ".$e->getMessage();
         }
      }
      return (object)$response;

   }

   private function getToken(array $configs): Array
   {

      $response=['access_token'=>'',
                     'expires_in'=>''];

      try {
         $fullURL = $$configs['baseURL']."token/";
         $apiResponse = Http::timeout($configs['timeout'])
            ->withBasicAuth($$configs['clientUserName'], $configs['clientPassword'])
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
               'baseURL'=>\env('MTN_BASE_URL'),
               'clientId'=>\env(\strtoupper($urlPrefix).'_MTN_OcpKey'),
               'clientUserName'=>\env(\strtoupper($urlPrefix).'_MTN_USERNAME'),
               'clientPassword'=>\env(\strtoupper($urlPrefix).'_MTN_PASSWORD'),
               'targetEnv'=>\env('MTN_TARGET_ENVIRONMENT'),
               'timeout'=>\env('MTN_Http_Timeout'),
               'txCurrency'=>\env('MTN_CURRENCY')
         ];
   }

}