<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;

use Exception;

class Swasco implements IBillingClient
{

   private $districts =[
      "BAT"=>"BATOKA",
      "CHI"=>"CHISEKESI",
      "CHK"=>"CHIKANKATA",
      "CHO"=>"CHOMA",
      "CHR"=>"CHIRUNDU",
      "GWE"=>"GWEMBE",
      "ITT"=>"ITEZHITEZHI",
      "KAL"=>"KALOMO",
      "KAZ"=>"KAZUNGULA",
      "LIV"=>"LIVINGSTONE",
      "MAG"=>"MAGOYE",
      "MAZ"=>"MAZABUKA",
      "MAM"=>"MAAMBA",
      "MBL"=>"MBABALA",
      "MUN"=>"MUNYUMBWE",
      "MZE"=>"MONZE",
      "NAM"=>"NAMWALA",
      "NEG"=>"NEGANEGA",
      "PEM"=>"PEMBA",
      "SIA"=>"SIAVONGA",
      "SZE"=>"SINAZEZE",
      "SIN"=>"SINAZONGWE",
      "ZIM"=>"ZIMBA",
   ];

   public function __construct(
      private BillingCredentialService $billingCredentialsService) 
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {
         if(!(\strlen($params['customerAccount'])==10)){
            throw new Exception("Invalid SWASCo account number",1);
         }
         $configs = $this->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL'] . "navision/customers/".\rawurlencode($params['customerAccount']);
         $apiResponse = Http::timeout($configs['swascoTimeout'])->withHeaders([
                                             'Content-Type' => 'application/json',
                                             'Accept' => '*/*'
                                       ])->get($fullURL);

         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            $response = $apiResponse['data']['customer'];
            $response['district']=$this->getDistrict(\substr($response['accountNumber'],0,3));
         } else {
            if ($apiResponse->status() == 404 || $apiResponse->status() == 500) {
               $apiResponse = $apiResponse->json();
               if ($apiResponse['status']['code'] == 404) {
                  throw new Exception("Invalid SWASCo account number", 1);
               } else {
                  throw new Exception($apiResponse['status']['message'], 2);
               }
            } else {
               throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 2);
            }
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1 || $e->getCode() == 2) {
            throw $e;
         } else {
            throw new Exception("Error executing 'Get Account Details': " . $e->getMessage(), 3);
         }
      }

      return $response;
   }

   public function postPayment(Array $postParams): Array 
   {

      $response=[
               'status'=>'FAILED',
               'receiptNumber'=>'',
               'error'=>''
         ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         switch ($postParams['paymentType']) {
               case '1':
                  $fullURL = $configs['baseURL'] . "navision/payments/bills";
                  break;
               case '4':
                  $fullURL = $configs['baseURL'] . "navision/payments/reconnections";
                  break;
               case '5':
                  $fullURL = $configs['baseURL'] . "navision/payments/waterconnections";
                  break;                    
               case '6':
                  $fullURL = $configs['baseURL'] . "navision/payments/sewerconnections";
                  break;
               case '8':
                  $fullURL = $configs['baseURL'] . "navision/payments/capitalcontributions";
                  break;
               case '12':
                  $fullURL = $configs['baseURL'] . "navision/payments/vacuumtankers";
                  break;                                      
               default:
                  $fullURL = $configs['baseURL'] . "navision/payments/bills";
                  break;
         }
         
         $apiResponse = Http::timeout($configs['receiptingTimeout'])
                                 ->withHeaders([
                                       'Content-Type' => 'application/json',
                                       'Accept' => '*/*',
                                    ])
                                 ->post($fullURL, [
                                       'accountNumber' => $postParams['account'],
                                       'amount' => (string)$postParams['amount'],
                                       "mobileNumber" => $postParams['mobileNumber'],
                                       "referenceNumber" => $postParams['referenceNumber'],
                                       "paymentType" => $postParams['paymentType']
                                    ]);
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            $response['status']="SUCCESS";
            $response['receiptNumber']=$apiResponse['data']['ReceiptNo'];
         } else {
            if ($apiResponse->status() == 500) {
               $apiResponse = $apiResponse->json();
               throw new Exception($apiResponse['status']['message']);
            } else {
               throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 1);
            }
         }
      } catch (\Throwable $e) {
         $response['error']=$e->getMessage();
      }
      return $response;
   }

   public function postComplaint(array $postParams): String {
      $response ="";
      try {
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseURL'] . "navision/complaints";
         $apiResponse = Http::timeout($configs['swascoTimeout'])
                              ->withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Accept' => '*/*',
                                 ])
                              ->post($fullURL,[
                                    'accountNumber' => $postParams['customerAccount'],
                                    'complaintCode' => $postParams['complaintCode'],
                                    "mobileNumber" => $postParams['mobileNumber']
                                 ]);

         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               $response = $apiResponse['data']['referenceNo'];
         } else {
               if ($apiResponse->status() == 500) {
                  $apiResponse = $apiResponse->json();
                  throw new Exception($apiResponse['status']['message']);
               } else {
                  throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 1);
               }
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   public function changeCustomerDetail(array $postParams): String {

      $response = "";
      try {


         //$response = 'CASE2929292929';
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseURL'] . "navision/mobilenos";
         $apiResponse = Http::timeout($configs['swascoTimeout'])
                              ->withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Accept' => '*/*',
                                 ])
                              ->post($fullURL, [
                                    'accountNumber' => $postParams['customerAccount'],
                                    'mobileNumber' => $postParams['mobileNumber'],
                                    'newMobileNumber' => $postParams['newMobileNumber'],
                                 ]);
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               $response = $apiResponse['data']['referenceNo'];
         } else {
               if ($apiResponse->status() == 500) {
                  $apiResponse = $apiResponse->json();
                  throw new Exception($apiResponse['status']['message']);
               } else {
                  throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 1);
               }
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   public function getDistrict(String $code): string
   {
      
      if(\array_key_exists($code,$this->districts)){
         return $this->districts[$code];
      }else{
         return "OTHER";
      }
      
   }

   private function getConfigs(string $client_id):array
   {

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $configs['receiptingTimeout'] = $clientCredentials['RECEIPTING_TIMEOUT'];
      $configs['swascoTimeout'] = $clientCredentials['REMOTE_TIMEOUT'];
      $configs['baseURL'] = $clientCredentials['POSTPAID_BASE_URL'];
      return $configs;

   }

}
