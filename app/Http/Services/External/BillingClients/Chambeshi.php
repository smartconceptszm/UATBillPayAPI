<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;

use Exception;

class Chambeshi implements IBillingClient
{
    
  
   public function getAccountDetails(string $accountNumber): array
   {

      $response = [];

      try {

         //Populate the Response Array


         $response = [
                           "accountNumber" => $accountNumber,
                           "name"=>"Nzima",
                           "address"=>"CBD Kasama",
                           "district" => 'CHAMBESHI',
                           "mobileNumber"=>"0972702707",
                           "balance"=> 66.78,
                     ];

      
      } catch (\Throwable $e) {
         if ($e->getCode() == 2) {
               throw new Exception($e->getMessage(), 2);
         } elseif ($e->getCode() == 1) {
               throw new Exception($e->getMessage(), 1);
         } else {
               throw new Exception("Error executing 'Get Account Details': " . $e->getMessage(), 1);
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
         switch ($postParams['paymentType']) {
               case '1':
                  $fullURL = $this->baseURL . "navision/payments/bills";
                  break;
               case '4':
                  $fullURL = $this->baseURL . "navision/payments/reconnections";
                  break;
               case '5':
                  $fullURL = $this->baseURL . "navision/payments/waterconnections";
                  break;                    
               case '6':
                  $fullURL = $this->baseURL . "navision/payments/sewerconnections";
                  break;
               case '8':
                  $fullURL = $this->baseURL . "navision/payments/capitalcontributions";
                  break;
               case '12':
                  $fullURL = $this->baseURL . "navision/payments/vacuumtankers";
                  break;                                      
               default:
                  $fullURL = $this->baseURL . "navision/payments/bills";
                  break;
         }
         $apiResponse = Http::timeout($this->swascoReceiptingTimeout)
               ->withHeaders([
                  'Content-Type' => 'application/json',
                  'Accept' => '/',
               ])
               ->post($fullURL, [
                  'accountNumber' => $postParams['account'],
                  'amount' => $postParams['amount'],
                  "mobileNumber" => $postParams['mobileNumber'],
                  "referenceNumber" => $postParams['reference'],
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


}