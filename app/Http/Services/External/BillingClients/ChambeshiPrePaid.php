<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;

use Exception;

class ChambeshiPrePaid implements IBillingClient
{
    
  
   private $districts =[
      "KCT"=>"KASAMA",
      "CHA"=>"CHAMBESHI", 
   ];

   public function __construct(
         private string $baseURL,
         private string $username,
         private string $password,
      )
   {}

   public function getDistrict(String $code): string
   {
      return $this->districts[$code];
   }

   public function getAccountDetails(string $accountNumber): array
   {

      $response = [];

      try {

      //CHECKING STRING LENGTH
         // if(!(\strlen($accountNumber)==13)){
         //       throw new Exception("Invalid Account Number",1);
         // }

         $fullURL = $this->baseURL."pos_preview";
         $postData = [
                     "user_name"=> $this->username,
                     "password" =>$this->password,
                     "meter_number" => $accountNumber,
                     "total_paid" =>"150",
                     "debt_percent" =>"50"
                     ];

         $apiResponse  = Http::withHeaders([
                                             'Content-Type' => 'application/json',
                                             'Accept' => '*/*',
                                       ])->post($fullURL , $postData);
                  
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['result_code']  == 0){
                  $response['accountNumber'] = $apiResponse['result']['customer_number'];
                  $response['name'] = $apiResponse['result']['customer_name'];
                  $response['address'] = $apiResponse['result']['customer_addr'];
                  $response['district']="OTHER";
                  $response['mobileNumber'] = "";
                  $response['balance'] = $apiResponse['result']['debt_total'];
                 
               }
               else{
                  throw new Exception($apiResponse['result']['reason'], 1); 
               }
              
         } else {
            throw new Exception("CHAMBESHI PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }
      } catch (Exception $e) {
         if ($e->getCode() == 2) {
               throw new Exception($e->getMessage(), 2);
         } elseif ($e->getCode() == 1) {
               throw new Exception($e->getMessage(), 1);
         } else {
               throw new Exception("Error executing 'Get PrePaid Account Details': " . $e->getMessage(), 1);
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

      //CHECKING STRING LENGTH
         // if(!(\strlen($accountNumber)==13)){
         //       throw new Exception("Invalid Account Number",1);
         // }

         $fullURL = $this->baseURL."pos_purchase";
         $postData = $postParams;

         $apiResponse  = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
        ])->post($fullURL , $postData);
                  

    

         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['result_code']  == 0){
                  $response['status'] = "SUCCESS";
                  $response['receiptNumber'] = $apiResponse['result']['token'];
                 
               }
               else{
                  throw new Exception($apiResponse['result']['reason'], 1); 
               }
              
         } else {
            throw new Exception("CHAMBESHI PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }
      } catch (Exception $e) {
         if ($e->getCode() == 2) {
               throw new Exception($e->getMessage(), 2);
         } elseif ($e->getCode() == 1) {
               throw new Exception($e->getMessage(), 1);
         } else {
               throw new Exception("Error executing 'Error Getting Token': " . $e->getMessage(), 1);
         }
      }

      return $response;
 
   }


}