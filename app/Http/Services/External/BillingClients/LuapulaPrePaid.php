<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor;
use App\Http\Services\External\BillingClients\LuapulaPostPaid;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Clients\ClientCustomerService;
use Illuminate\Support\Facades\Http;
use Exception;

class LuapulaPrePaid implements IBillingClient
{

   public function __construct(
         private BillingCredentialService $billingCredentialsService,
         private ClientCustomerService $clientCustomerService,
         private PurchaseEncryptor $purchaseEncryptor,
         private LuapulaPostPaid $luapulaPostPaid)
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];
      try {
         $configs = $this->getConfigs($params['client_id']);
         $authorization =  $this->login($configs);
         $fullURL = $configs['baseURL']."purchase/calculatefee";
         $postData = [
                        "meterNumber" => $params['customerAccount'],
                        "purchaseType" =>1,
                        "purchaseValue" =>$params['paymentAmount']
                     ];

         $apiResponse  = Http::timeout($configs['timeout'])
                                 ->withHeaders([
                                    "Accept-APIVersion" => $configs['apiVersion'],
                                    'Content-Type' => 'application/json',
                                    'Authorization' => $authorization,
                                    'Accept' => '*/*',
                                 ])->post($fullURL , $postData);
                  
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if($apiResponse["errorCode"] == '10000'){
               $configs['authorization'] = $authorization;
               $customerDetails = $this->getCustomerDetails($params,$configs);
               $clientCustomer = $this->clientCustomerService->findOneBy(['customerAccount'=>$params['customerAccount']]);
               $revenuePoint = 'OTHER';
               $consumerTier = 'OTHER';
               $consumerType = 'OTHER';
               $fullAddress = $customerDetails['data']['address'];
               if($clientCustomer){
                  $revenuePoint = $clientCustomer->revenuePoint;
                  $consumerTier = $clientCustomer->consumerTier;
                  $consumerType = $clientCustomer->consumerType;
               }
               $response['customerAccount'] = $apiResponse['data']['customerNumber'];
               $response['name'] = $customerDetails['data']['name'];
               $response['revenuePoint'] = $revenuePoint;
               $response['consumerTier'] = $consumerTier;
               $response['consumerType'] = $consumerType;
               $response['address'] = $fullAddress;
               $response['mobileNumber'] = "";
               $response['balance'] = \number_format((float)$customerDetails['data']['debt']) ;
            }else{
               $this->processError($apiResponse);
            }
         } else {
            throw new Exception("Luapula PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {

         switch ($e->getCode()) {
            case 1:
               throw $e;
               break;
            case 2:
               throw  $e;
               break;
            default:
               throw new Exception("Error executing 'Get PrePaid Account Details': " . $e->getMessage(), 2);
               break;
         }

      }
      return $response;

   }

   private function getCustomerDetails(array $params, array $configs): array
   {

      try {
         $fullURL = $configs['baseURL']."customers/querycustomerbymeternumber";
         $postData = [
                        "meterNumber" => $params['customerAccount']
                     ];

         $apiResponse  = Http::timeout($configs['timeout'])
                                 ->withHeaders([
                                    "Accept-APIVersion" => $configs['apiVersion'],
                                    'Content-Type' => 'application/json',
                                    'Authorization' => $configs['authorization'],
                                    'Accept' => '*/*',
                                 ])->post($fullURL , $postData);
                  
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if($apiResponse["errorCode"] == '10000'){
               return $apiResponse;
            }else{
               $this->processError($apiResponse);
            }
         } else {
            throw new Exception("Luapula PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {

         switch ($e->getCode()) {
            case 1:
               throw $e;
               break;
            case 2:
               throw  $e;
               break;
            default:
               throw new Exception("Error executing 'Get PrePaid Account Details': " . $e->getMessage(), 2);
               break;
         }

      }

   }
   
   public function generateToken(Array $postParams): Array 
   {

      $response=[
         'status'=>'FAILED',
         'tokenNumber'=>'',
         'error'=>''
      ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         $authorization =  $this->login($configs);
         $purchaseParameterString = $this->purchaseEncryptor->generatePurchaseString(
                                                                  $postParams['transactionId'],
                                                                  $postParams['paymentAmount'],  
                                                                  $configs['rootKey']);
         $postData = [
                        "meterNumber" => $postParams['customerAccount'],
                        "transID" => $postParams['transactionId'],
                        "purchaseType" => $configs['purchaseType'],
                        "purchaseParam" =>$purchaseParameterString
                     ];
         $fullURL = $configs['baseURL']."purchase/executepurchase";
         $apiResponse  = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    "Accept-APIVersion" => $configs['apiVersion'],
                                    'Content-Type' => 'application/json',
                                    'Authorization' => $authorization,
                                    'Accept' => '*/*',
                              ])->post($fullURL , $postData);

         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['errorCode']  == 10000){
                  $tokenArr = \explode(",", $apiResponse['data']['tokenList']);
                  $formattedTokens = [];
                  foreach ($tokenArr as $value) {
                     $formattedTokens[]=\implode('-', \str_split(str_replace(' ', '', $value), 4));
                  }
                  $response['tokenNumber'] = \implode(',',$formattedTokens);
                  $response['status'] = "SUCCESS";
               }
               else{
                  $this->processError($apiResponse);
               }
         } else {
            throw new Exception("LUAPULA PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         $response['error'] = "Error executing 'Error Getting Token': " . $e->getMessage();
      }

      return $response;
 
   }

   public function postPayment(array $postParams): array
   {
      return $this->luapulaPostPaid->postPayment($postParams);
   }

   private function processError(array $apiResponse)
   {

      switch ($apiResponse["errorCode"]) {
         case 12320:
            throw new Exception("Invalid Luapula PRE-PAID Meter Number",1); 
            break;
         case 12902:
            throw new Exception("Invalid Luapula PRE-PAID Meter Number",1); 
            break;
         case 12562:
            throw new Exception("Payment is too little, less than additional fee",4);
            break;
         case 12563:
            throw new Exception("Payment is too little, less than minimum purchase",4);
            break;
         case 12561:
            throw new Exception("Payment is too high, exceeds maximum purchase",4);
            break;
         default:
            throw new Exception($apiResponse["msg"],2);
            break;
      }
       
   }

   private function login($configs): string
   {  

      $response ="";

      try {
         $fullURL = $configs['baseURL'] . "thirdplatforms/login";
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    "Accept-APIVersion" => $configs['apiVersion'],
                                    "Content-Type" => "application/json",
                                    "Accept" => "*/*"
                                 ])
                              ->post($fullURL, [
                                    "platformID" => $configs['platformId']
                                 ]);
         if ($apiResponse->status() >= 200 && $apiResponse->status() < 300) {
            $jsonResponse = $apiResponse->json();
            if($jsonResponse['errorCode'] == '10000'){
               $response = $apiResponse->header('Authorization');
            }
         } else {
               throw new Exception("LUKANGA PrePaid API Get Login error. LUKANGA PrePaid Service responded with status: " . $apiResponse->status().".",1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
               throw new Exception($e->getMessage(), 1);
         }else{
               throw new Exception("LUKANGA PrePaid API Get Token error. Details: " . $e->getMessage(), 1);
         }
      }
      return $response;
   }

   private function getConfigs(string $client_id):array
   {

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $configs['purchaseType'] = (int)$clientCredentials['PREPAID_PURCHASE_TYPE'];
      $configs['apiVersion'] = $clientCredentials['PREPAID_API_VERSION'];
      $configs['platformId'] = $clientCredentials['PREPAID_PLATFORMID'];
      $configs['rootKey'] = $clientCredentials['PREPAID_ROOTKEY'];
      $configs['baseURL'] = $clientCredentials['PREPAID_BASE_URL'];
      $configs['timeout'] = $clientCredentials['PREPAID_TIMEOUT'];

      return $configs;
       
   }

}