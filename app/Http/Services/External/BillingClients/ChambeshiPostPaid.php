<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;
use Exception;

class ChambeshiPostPaid implements IBillingClient
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

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {     
         $configs = $this->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL']."/lookup?query=".$params['customerAccount'];
         $apiResponse = Http::timeout($configs['timeout'])
                                 ->withHeaders([
                                    'Accept' =>  '*/*',
                                 ])
                              ->get($fullURL);
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if(isset($apiResponse['customer_name'])){
               $revenuePoint = $this->getRevenuePoint($params['customerAccount']);
               $response['customerAccount'] = $params['customerAccount'];
               $response['name'] = $apiResponse['customer_name'];
               $response['address'] = "";
               $response['revenuePoint'] = $revenuePoint;
               $response['composite'] = 'ORDINARY';
               $response['consumerTier'] = '';
               $response['consumerType'] = '';
               $response['mobileNumber'] =  "";
               $response['balance'] = \number_format((float)$apiResponse['balance'], 2, '.', ',');
            }else{
               if(isset($apiResponse['status']) && $apiResponse['status']=='ERROR'){
                  throw new Exception('Chambeshi POST-PAID '.$apiResponse['response'], 1);
               }else{
                  throw new Exception("Chambeshi POST-PAID Account Number not found", 1);
               }

            }
         } else {
            throw new Exception("status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            throw $e;
         }else{
            throw new Exception("Error executing 'Get Account Details': " . $e->getMessage(), 2);
         }
      }

      return $response;
   }

   public function postPayment(array $postParams): array
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
            $apiResponse =  Http::timeout($configs['timeout'])
                                 ->withHeaders([
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
                     throw new Exception('Chambeshi Post-Paid server error. Details: '.$apiResponse['response'],1);
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
      $configs['timeout'] = $clientCredentials['POSTPAID_TIMEOUT'];
      return $configs;
   }

}