<?php

namespace App\Http\Services\External\BillingClients;
use App\Http\Services\External\BillingClients\Nkana\PurchaseEncryptor;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Exception;

class NkanaPrePaid implements IBillingClient
{

   private $getCustomerFunction = "querycustomerbymeternumber";
   private $purchasePreview = "platformcalculatefee";

   public function __construct(
         private string $baseURL,
         private string $platformId,
         private PurchaseEncryptor $purchaseEncryptor
      )
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {

         $getData = [
                        "function"=> $this->getCustomerFunction,
                        "platformid" =>$this->platformId,
                        "meternumber" => $params['meterNumber'],
                     ];

         $apiResponse = Http::withHeaders([
                                    'Accept' => '*/*'
                                 ])->get($this->baseURL, $getData);

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
                        $response['accountNumber'] = $apiResponseArray['identificationnumber'];
                        $response['name'] = $apiResponseArray['customername'];
                        $response['address'] = "KITWE";
                        $response['district'] = "KITWE";
                        $response['mobileNumber'] =  $apiResponseArray['telephonenumber'];
                        $response['balance'] = $apiResponseArray['debt'];
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
                  throw new Exception("Lukanga PrePaid Service response could not be parsed into array without 'ErrorCode' Key", 2);
               }
            }else{
               throw new Exception("Lukanga PrePaid Service response could not be parsed into array", 2);
            }
            // 4 Communication failed
            // 10 Invalid Meter Number
            // 11 CustomerNotExist
            // 12 customer account’s status is unnormal
            // 13 Invalid platform ID
            // 20 InvalidPayment
            // 22 Payment is too much,more than max-purchase limitation
            // 23 Payment is too little, less than additional fee
            // 45 Exist outdoor task
         } else {
            throw new Exception("LUKANGA PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 2) {
            throw new Exception($e->getMessage(), 2);
         } elseif ($e->getCode() == 1) {
            throw new Exception($e->getMessage(), 1);
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

         $purchaseParameterString = $this->purchaseEncryptor->generatePurchaseString(
                                          $postParams['transactionId'], $postParams['paymentAmount']);
                                          
         $tokenParameters = [
                              "operatetype"=>"purchasebytransid",
                              "platformid" =>$this->platformId,
                              "meternumber" =>  $postParams['meterNumber'],
                              "transid" => $postParams['transactionId'],
                              "purchaseparam" => $purchaseParameterString
                           ];
         $apiResponse = Http::asForm()->post($this->baseURL, $tokenParameters);

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
                        $response['status'] = "SUCCESS";
                        $response['tokenNumber'] = \implode('-', \str_split($apiResponseArray['tokenlist'], 4));
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

   public function postPayment(Array $postParams): Array
   {

      $response=[
            'status'=>'SUCCESS',
            'receiptNumber'=>"RCPT".\rand(1000,100000),
            'error'=>''
         ];

      return $response;
   }

   public function getAccountPosPreview(array $params): array
   {

      $response = [];

      try {

         $getData = [
                        "function"=> $this->purchasePreview,
                        "platformid" =>$this->platformId,
                        "meternumber" => $params['meterNumber'],
                        "payment" => $params['paymentAmount'],
                     ];

         $apiResponse = Http::withHeaders([
                                    'Accept' => '*/*'
                                 ])->get($this->baseURL, $getData);

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
                        $response['accountNumber'] = $apiResponseArray['identificationnumber'];
                        $response['name'] = $apiResponseArray['customername'];
                        $response['address'] = "KITWE";
                        $response['district'] = "KITWE";
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
            // 4 Communication failed
            // 10 Invalid Meter Number
            // 11 CustomerNotExist
            // 12 customer account’s status is unnormal
            // 13 Invalid platform ID
            // 20 InvalidPayment
            // 22 Payment is too much,more than max-purchase limitation
            // 23 Payment is too little, less than additional fee
            // 45 Exist outdoor task


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



}

