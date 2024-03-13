<?php

namespace App\Http\Services\External\BillingClients;

use \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService;
use App\Http\Services\External\BillingClients\Chambeshi\Chambeshi;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;

use Exception;

class ChambeshiPrePaid extends Chambeshi implements IBillingClient
{
    
   public function __construct(
         private string $baseURL,
         private string $username,
         private string $password,
         private string $passwordVend,
         protected ChambeshiPaymentService $chambeshiPaymentService
      )
   {}

   public function getAccountDetails(string $meterNumber): array
   {

      $response = [];

      try {
         $fullURL = $this->baseURL."pos_preview";
         $postData = [
                        "user_name"=> $this->username,
                        "password" =>$this->password,
                        "meter_number" => $meterNumber,
                        "total_paid" =>"20.00",
                        "debt_percent" =>"50"
                     ];

         $apiResponse  = Http::withHeaders([
                                             'Content-Type' => 'application/json',
                                             'Accept' => '*/*',
                                       ])->post($fullURL , $postData);
                  
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               switch ($apiResponse['result_code']) {
                  case 0:
                     $response['accountNumber'] = $apiResponse['result']['customer_number'];
                     $response['name'] = $apiResponse['result']['customer_name'];
                     $response['address'] = $apiResponse['result']['customer_addr'];
                     $response['district'] = $this->getDistrict(\trim($apiResponse['result']['customer_number']));
                     $response['mobileNumber'] = "";
                     $response['balance'] = \number_format((float)$apiResponse['result']['debt_total'], 2, '.', ',');
                     break;
                  case 4:
                     throw new Exception($apiResponse['reason'], 1); 
                     break;
                  default:
                     throw new Exception($apiResponse['reason'], 2);
                     break;
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
               throw new Exception("Error executing 'Get PrePaid Account Details': " . $e->getMessage(), 2);
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
         $postParams["user_name"] = $this->username;
         $postParams["password"] = $this->password;
         $postParams["password_vend"] = $this->passwordVend;
         $fullURL = $this->baseURL."pos_purchase";
         $apiResponse  = Http::withHeaders([
                                    'Content-Type' => 'application/json',
                                    'Accept' => '*/*',
                              ])->post($fullURL , $postParams);

         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['result_code']  == 0){
                  $response['status'] = "SUCCESS";
                  $response['tokenNumber'] = $apiResponse['result']['token'];
               }
               else{
                  throw new Exception($apiResponse['result']['reason'], 1); 
               }
         } else {
            throw new Exception("CHAMBESHI PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (Exception $e) {
         $response['error'] = "Error executing 'Error Getting Token': " . $e->getMessage();
      }

      return $response;
 
   }

}