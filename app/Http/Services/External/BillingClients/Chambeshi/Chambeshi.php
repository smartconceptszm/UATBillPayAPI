<?php

namespace App\Http\Services\External\BillingClients\Chambeshi;

use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;
use Exception;

class Chambeshi
{

   private $revenuePoints =[
      "CHL"=>"Chilubi",
      "CHN"=>"Chinsali",
      "ISO"=>"Isoka",
      "KAP"=>"Kaputa",
      "KCT"=>"Kasama Central Town",
      "KMH"=>"Kasama Mulenga Hills",
      "LUW"=>"Luwingu",
      "MBA"=>"Mbala",
      "MPI"=>"Mpika", 
      "MPU"=>"Mpika", 
      "MPO"=>"Mporokoso", 
      "MUN"=>"Mpulungu", 
      "NAK"=>"Nakonde"
   ];

   public function __construct(
      private BillingCredentialService $billingCredentialService,
   )
   {}
  
   public function postPayment(Array $postParams): Array 
   {

      $response = [
                  'status'=>'FAILED',
                  'receiptNumber'=>'',
                  'error'=>''
               ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         unset($postParams['client_id']);
         $fullURL = $configs['baseURL']."/payment/";
         $postParams['username'] = $configs['username'];
         $postParams['password'] = $configs['password'];
         $apiResponse = Http::withHeaders([
                                 'Content-Type' => 'application/json',
                                 'Accept' =>  '*/*'
                              ])
                           ->post($fullURL, $postParams);
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               if(isset($apiResponse['status']) && $apiResponse['status'] == 'SUCCESS'){
                  $response['status']="SUCCESS";
                  $response['receiptNumber'] = $postParams['ReceiptNo'];
               }else{
                  throw new Exception('Chambeshi Post-Paid server error. Details: '.$apiResponse['RESPONSE'],1);
               }
         } else {
            throw new Exception(" Status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error'] = "Chambeshi Post-Paid server error. Details: " . $e->getMessage();
         }
      }
      return $response;
     
   }

   public function getRevenuePoint(String $customerAccount): string
   {

      try {
         $arrAccountCharacter = \str_split($customerAccount);
         $strCode = "";
         foreach ($arrAccountCharacter as $value) {
            if(\is_numeric($value)){
               break;
            }else{
               $strCode .= \strtoupper($value); 
            }
         }
         if(\array_key_exists($strCode,$this->revenuePoints)){
            return $this->revenuePoints[$strCode];
         }else{
            return "OTHER";
         }
      } catch (\Throwable $th) {
         return "OTHER";
      }

      
   }

   public function getConfigs(string $client_id):array
   {
      $clientCredentials = $this->billingCredentialService->getClientCredentials($client_id);
      $configs['username'] = $clientCredentials['POSTPAID_USERNAME'];
      $configs['password'] = $clientCredentials['POSTPAID_PASSWORD'];
      $configs['baseURL'] = $clientCredentials['POSTPAID_BASE_URL'];
      return $configs;
   }
   
}