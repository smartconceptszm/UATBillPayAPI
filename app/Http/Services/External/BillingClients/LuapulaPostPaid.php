<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Clients\ClientCustomerService;
use Illuminate\Support\Facades\Http;
use Exception;

class LuapulaPostPaid implements IBillingClient
{

   public function __construct(
         private BillingCredentialService $billingCredentialService,
         private ClientCustomerService $clientCustomerService)
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {     
         $configs = $this->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL']."/lookup?query=".$params['customerAccount'];
         $apiResponse = Http::timeout($configs['timeout'])
                                 ->withHeaders([
                                    'Accept' =>  '*/*',
                                 ])
                              ->get($fullURL);
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if(isset($apiResponse['customer_name'])){
               $clientCustomer = $this->clientCustomerService->findOneBy(['customerAccount'=>$params['customerAccount']]);
               $revenuePoint = 'OTHER';
               $consumerTier = 'OTHER';
               $consumerType = 'OTHER';
               $fullAddress = 'OTHER';
               if($clientCustomer){
                  $fullAddress = $clientCustomer->customerAddress;
                  $revenuePoint = $clientCustomer->revenuePoint;
                  $consumerTier = $clientCustomer->consumerTier;
                  $consumerType = $clientCustomer->consumerType;
               }

               $response['customerAccount'] = $params['customerAccount'];
               $response['name'] = $apiResponse['customer_name'];
               $response['composite'] = 'ORDINARY';
               $response['address'] = $fullAddress;
               $response['revenuePoint'] = $revenuePoint;
               $response['consumerTier'] = $consumerTier;
               $response['consumerType'] = $consumerType;
               $response['mobileNumber'] =  "";
               $response['balance'] = \number_format((float)$apiResponse['balance'], 2, '.', ',');
            }else{
               if(isset($apiResponse['status']) && $apiResponse['status']=='ERROR'){
                  throw new Exception('Luapula POST-PAID '.$apiResponse['response'], 1);
               }else{
                  throw new Exception("Luapula POST-PAID Account Number not found", 1);
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
      
      $response = [
                     'status'=>'FAILED',
                     'receiptNumber'=>'',
                     'error'=>''
                  ];

      try {
            $configs = $this->getConfigs($postParams['client_id']);
            unset($postParams['client_id']);
            $fullURL = $configs['baseURL']."payment/";
            $postParams['username'] = $configs['username'];
            $postParams['password'] = $configs['password'];
            $apiResponse =  Http::timeout($configs['timeout'])
                                 ->withHeaders([
                                       'Content-Type' => 'application/json',
                                       'Accept' =>  '*/*'
                                    ])
                                 ->post($fullURL, $postParams);
            if ($apiResponse->status() == 200) {
                  $apiResponse = $apiResponse->json();
                  if(isset($apiResponse['status']) && $apiResponse['status'] == 'SUCCESS'){
                     $response['status']="SUCCESS";
                     $response['receiptNumber'] = $postParams['ReceiptNo'];
                  }else{
                     throw new Exception('Luapula Post-Paid server error. Details: '.$apiResponse['response'],1);
                  }
            } else {
               throw new Exception(" Status code: " . $apiResponse->status(), 2);
            }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error'] = "Luapula Post-Paid server error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   public function getConfigs(string $client_id):array
   {

      $clientCredentials = $this->billingCredentialService->getClientCredentials($client_id);
      $configs['username'] = $clientCredentials['POSTPAID_USERNAME'];
      $configs['password'] = $clientCredentials['POSTPAID_PASSWORD'];
      $configs['baseURL'] = $clientCredentials['POSTPAID_BASE_URL'];
      $configs['timeout'] = $clientCredentials['POSTPAID_TIMEOUT'];
      return $configs;

   }

}