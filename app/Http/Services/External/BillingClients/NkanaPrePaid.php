<?php

namespace App\Http\Services\External\BillingClients;
use App\Http\Services\External\BillingClients\PrePaidVendor\PurchaseEncryptor;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;

use Exception;

class NkanaPrePaid implements IBillingClient
{

   public function __construct(
         private BillingCredentialService $billingCredentialsService,
         private PurchaseEncryptor $purchaseEncryptor)
   {}
   
   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {
         $configs = $this->getConfigs($params['client_id']);
         $getData = [
                        "function"=> "platformcalculatefee",
                        "platformid" =>$configs['platformId'],
                        "meternumber" => $params['customerAccount'],
                        "payment" => $params['paymentAmount'],
                     ];

         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                 'Accept' => '*/*'
                              ])
                              ->get($configs['baseURL'], $getData);

         if ($apiResponse->status() == 200) {
            $apiResponseString = $apiResponse->body(); // Get response data as BODY
            parse_str($apiResponseString, $apiResponseArray);
            if(\is_array($apiResponseArray)){
               if(\count($apiResponseArray) == 1){
                  $apiResponseArray = $apiResponse->json();
               }
               if(\array_key_exists('errorcode',$apiResponseArray)){
                  switch ($apiResponseArray['errorcode']) {
                     case "0":
                        $response['customerAccount'] = $apiResponseArray['identificationnumber'];
                        $response['name'] = $apiResponseArray['customername'];
                        $response['address'] = "KITWE";
                        $response['revenuePoint'] = "KITWE";
                        $response['consumerTier'] = '';
                        $response['consumerType'] = '';
                        $response['mobileNumber'] =  $apiResponseArray['telephonenumber'];
                        $response['balance'] =  $apiResponseArray['additionalfee'];
                        break;
                     case "3":
                        throw new Exception("Failed to calculate fee, increase amount",4);
                        break;
                     case "4":
                        throw new Exception("Connect LAPIS Server Failed",2);
                        break;
                     case "5":
                        throw new Exception("Can not save bill record into database",2);
                        break;
                     case "10":
                        throw new Exception("Invalid NKANA PRE-PAID Meter Number",1);
                        break;
                     case "11":
                        throw new Exception("Invalid NKANA PRE-PAID Meter Number",1);
                        break;
                     case "12":
                        throw new Exception("Customer account status is abnormal",1);
                        break;
                     case "13":
                        throw new Exception("Invalid platform ID",2);
                        break;
                     case "20":
                        throw new Exception("Invalid payment, increase amount",4);
                        break;
                     case "22":
                        throw new Exception("Payment is too much,more than max-purchase limitation",4);
                        break;
                     case "23":
                        throw new Exception("Payment is too little, less than additional fee",4);
                        break;
                     case "40":
                        throw new Exception("Invalid transaction ID",2);
                        break;
                     case "41":
                        throw new Exception("TransactionID had been Used",2);
                        break;
                     default:
                        throw new Exception("NKANA PrePaid Service responded with error code: " .$apiResponseArray['errorcode'], 2);
                        break;
                  }
               }else{
                  throw new Exception("Nkana PrePaid Service response could not be parsed into array without 'ErrorCode' Key", 2);
               }
            }else{
               throw new Exception("Nkana PrePaid Service response could not be parsed into array", 2);
            }
            
         } else {
            throw new Exception("NKANA PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1 || $e->getCode() == 2 || $e->getCode() == 4) {
            throw $e;
         } else {
            throw new Exception("Error executing 'Get PrePaid Account Details': " . $e->getMessage(), 3);
         }
      }

      return $response;
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
         $purchaseParameterString = $this->purchaseEncryptor->generatePurchaseString($postParams['transactionId'],
                                                                     $postParams['paymentAmount'],  $configs['rootKey']);
                                          
         $tokenParameters = [
                              "operatetype"=>"purchasebytransid",
                              "platformid" =>$configs['platformId'],
                              "meternumber" =>  $postParams['customerAccount'],
                              "transid" => $postParams['transactionId'],
                              "purchaseparam" => $purchaseParameterString
                           ];
         $apiResponse = Http::timeout($configs['timeout'])
                              ->asForm()
                              ->post($configs['baseURL'], $tokenParameters);

         if ($apiResponse->status() == 200) {
            $apiResponseString = $apiResponse->body(); // Get response data as BODY
            parse_str($apiResponseString, $apiResponseArray);
            if(\is_array($apiResponseArray)){
               if(\count($apiResponseArray) == 1){
                  $apiResponseArray = $apiResponse->json();
               }
               if(\array_key_exists('errorcode',$apiResponseArray)){
                  switch ($apiResponseArray['errorcode']) {
                     case "0":
                        $tokenArr = \explode(",", $apiResponseArray['tokenlist']);
                        $formattedTokens = [];
                        foreach ($tokenArr as $value) {
                           $formattedTokens[]=\implode('-', \str_split(str_replace(' ', '', $value), 4));
                        }
                        $response['tokenNumber'] = \implode(',',$formattedTokens);
                        $response['status'] = "SUCCESS";
                        break;
                     case "3":
                        throw new Exception("Failed to calculate fee, increase amount",4);
                        break;
                     case "4":
                        throw new Exception("Connect LAPIS Server Failed",2);
                        break;
                     case "5":
                        throw new Exception("Can not save bill record into database",2);
                        break;
                     case "10":
                        throw new Exception("Invalid NKANA PRE-PAID Meter Number",1);
                        break;
                     case "11":
                        throw new Exception("Invalid NKANA PRE-PAID Meter Number",1);
                        break;
                     case "12":
                        throw new Exception("Customer account status is abnormal",1);
                        break;
                     case "13":
                        throw new Exception("Invalid platform ID",2);
                        break;
                     case "20":
                        throw new Exception("Invalid payment, increase amount",4);
                        break;
                     case "22":
                        throw new Exception("Payment is too much,more than max-purchase limitation",4);
                        break;
                     case "23":
                        throw new Exception("Payment is too little, less than additional fee",4);
                        break;
                     case "40":
                        throw new Exception("Invalid transaction ID",2);
                        break;
                     case "41":
                        throw new Exception("TransactionID had been Used",2);
                        break;
                     default:
                        throw new Exception("NKANA PrePaid Service responded with error code: " .$apiResponseArray['errorcode'], 2);
                        break;
                  }
               }else{
                  throw new Exception("Nkana PrePaid Service response could not be parsed into array without 'ErrorCode' Key", 2);
               }
            }else{
               throw new Exception("Nkana PrePaid Service response could not be parsed into array", 2);
            }
         } else {
            throw new Exception("NKANA PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1 || $e->getCode() == 2 || $e->getCode() == 4) {
            $response['error'] = $e->getMessage();
         } else {
            $response['error'] = " NKANA PrePaid Service error. Details: " . $e->getMessage();
         }
      }

      return $response;

   }

   public function postPayment(Array $postParams): Array
   {

      $response=[
            'status'=>'SUCCESS',
            'receiptNumber'=>"RCPT".\rand(1000,100000),
            'error'=>''
         ];

      return $response;
   }

   private function getConfigs(string $client_id):array
   {

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $configs['platformId'] = $clientCredentials['PREPAID_PLATFORMID'];
      $configs['rootKey'] = $clientCredentials['PREPAID_ROOTKEY'];
      $configs['baseURL'] =$clientCredentials['PREPAID_BASE_URL'];
      $configs['timeout'] = $clientCredentials['PREPAID_TIMEOUT'];
      return $configs;
   }


}

