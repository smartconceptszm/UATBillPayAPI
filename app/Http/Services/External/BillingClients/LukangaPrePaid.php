<?php

namespace App\Http\Services\External\BillingClients;
use App\Http\Services\External\BillingClients\Lukanga\PurchaseEncryptor;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Exception;

class LukangaPrePaid implements IBillingClient
{

   private $getCustomerFunction = "querycustomerbymeternumber";

   public function __construct(
         private string $baseURL,
         private string $platformId,
         private PurchaseEncryptor $purchaseEncryptor
      )
   {}

   public function getAccountDetails(string $meterNumber): array
   {

      $response = [];

      try {

         $getData = [
                        "function"=> $this->getCustomerFunction,
                        "platformid" =>$this->platformId,
                        "meternumber" => $meterNumber,
                     ];

         $apiResponse = Http::withHeaders([
                                    'Accept' => '*/*'
                                 ])->get($this->baseURL, $getData);

         if ($apiResponse->status() == 200) {
            $apiResponseString = $apiResponse->body(); // Get response data as BODY
            parse_str($apiResponseString, $apiResponseArray);
            switch ($apiResponseArray['errorcode']) {
               case "0":
                  $response['accountNumber'] = $apiResponseArray['identificationnumber'];
                  $response['name'] = $apiResponseArray['customername'];
                  $response['address'] = "CENTRAL";
                  $response['district'] = "CENTRAL";
                  $response['mobileNumber'] =  $apiResponseArray['telephonenumber'];
                  $response['balance'] = $apiResponseArray['debt'];
                  break;
               case "10":
                  throw new Exception("Invalid Meter Number",1);
                  break;
               case "11":
                  throw new Exception("Customer does not exist",1);
                  break;
               default:
                  throw new Exception("LUKANGA PrePaid Service responded with error code: " .$apiResponseArray['errorcode'], 2);
                  break;
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

      } catch (Exception $e) {
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
            // $apiResponseString = $apiResponse->body(); // Get response data as BODY
            // parse_str($apiResponseString, $apiResponseArray);
            $apiResponseArray = $apiResponse->json();
            if($apiResponseArray['errorcode']==0){
                //Populate the Array with  Receipt data
               $response['status'] = "SUCCESS";
               $response['tokenNumber'] = \implode('-', \str_split($apiResponseArray['tokenlist'], 4));
               // $response['repaydebt'] = $apiResponseArray['repaydebt'];
               // $response['additionalfee'] = $apiResponseArray['additionalfee'];
               // $response['rechargeamount'] = $apiResponseArray['rechargeamount'];
               // $response['rechargevolume'] = $apiResponseArray['rechargevolume'];
               // $response['vatrate'] = $apiResponseArray['vatrate'];
               // $response['vatamount'] = $apiResponseArray['vatamount'];

            }else {
               // 3 CalculateFeeFailed
               // 4 Connect LAPIS Server Failed
               // 5 Can’t save bill record into database
               // 10 Invalid Meter Number
               // 11 CustomerNotExist
               // 12 customer account’s status is unnormal
               // 13 Invalid platform ID
               // 20 InvalidPayment
               // 22 Payment is too much,more than max-purchase limitation
               // 23 Payment is too little, less than additional fee
               // 40 Invalid transaction ID
               // 41 TransactionID had been Used
               // 42 Decrypt failed, maybe root key is invalid
               throw new Exception("LUKANGA PrePaid Service responded with error code: ".$apiResponseArray['errorcode'], 2);
            }


         } else {
            throw new Exception("LUKANGA PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (Exception $e) {
         throw new Exception("Error executing 'Error Getting Token': " . $e->getMessage());
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


}

