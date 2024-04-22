<?php

namespace App\Http\Services\External\BillingClients;

use \App\Http\Services\External\BillingClients\Chambeshi\ChambeshiPaymentService;
use App\Http\Services\External\BillingClients\Chambeshi\Chambeshi;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;

use Exception;

class ChambeshiPrePaid extends Chambeshi implements IBillingClient
{

   private string $passwordVend;
   private string $username;
   private string $password;
   private string $baseURL;

   public function __construct(
         protected ChambeshiPaymentService $chambeshiPaymentService
      )
   {

      $this->passwordVend = \env('CHAMBESHI_PREPAID_PASSWORD_VEND');
      $this->username = \env('CHAMBESHI_PREPAID_USERNAME');
      $this->password = \env('CHAMBESHI_PREPAID_PASSWORD');
      $this->baseURL = \env('CHAMBESHI_PREPAID_BASE_URL');
   }

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {
         $fullURL = $this->baseURL."pos_preview";
         $postData = [
                        "user_name"=> $this->username,
                        "password" =>$this->password,
                        "meter_number" => $params['meterNumber'],
                        "total_paid" =>$params['paymentAmount'],
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
                     $response['balance'] = \number_format((float)$apiResponse['result']['debt_remain'] + (float)$apiResponse['result']['monthly_charge'], 2, '.', ',') ;
                     break;
                  case 4:
                     throw new Exception("Invalid Chambeshi PRE-PAID Meter Number", 1); 
                     break;
                  case 6:
                        throw new  Exception($apiResponse['reason'],  4); 
                        break;
                  default:
                     throw new Exception($apiResponse['reason'], 2);
                     break;
               }
         } else {
            throw new Exception("CHAMBESHI PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {
         switch ($e->getCode()) {
            case 1:
               throw $e;
               break;
            case 2:
               throw  $e;
               break;
            case 4:
               throw  $e;
               break;
            default:
               throw new Exception("Error executing 'Get PrePaid Account Details': " . $e->getMessage(), 2);
               break;
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
                  throw new Exception($apiResponse['reason'], 1); 
               }
         } else {
            throw new Exception("CHAMBESHI PrePaid Service responded with status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         $response['error'] = "Error executing 'Error Getting Token': " . $e->getMessage();
      }

      return $response;
 
   }


}