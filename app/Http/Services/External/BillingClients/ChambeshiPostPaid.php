<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\Chambeshi\Chambeshi;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;
use Exception;

class ChambeshiPostPaid implements IBillingClient
{
    
   public function __construct(
         private Chambeshi $chambeshi,
      )
   {}
  
   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {     
         $configs = $this->chambeshi->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL']."/lookup?query=".$params['customerAccount'];
         $apiResponse = Http::withHeaders([
                                    'Accept' =>  '*/*',
                                 ])
                              ->get($fullURL);
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if(isset($apiResponse['customer_name'])){
               $revenuePoint = $this->chambeshi->getRevenuePoint($params['customerAccount']);
               $response['customerAccount'] = $params['customerAccount'];
               $response['name'] = $apiResponse['customer_name'];
               $response['address'] = "";
               $response['revenuePoint'] = $revenuePoint;
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
      return $this->chambeshi->postPayment($postParams);
   }

}