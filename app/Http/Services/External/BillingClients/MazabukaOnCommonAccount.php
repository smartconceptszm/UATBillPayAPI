<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;

use Exception;

class MazabukaOnCommonAccount implements IBillingClient
{
    
   public function __construct()
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [
                     'customerAccount' => $params['customerAccount'],
                     "name" => "Mazabuka Customer",
                     "address" => "MAZABUKA",
                     "revenuePoint" => 'MAZABUKA',
                     "consumerTier" => '',
                     "consumerType" => '',
                     "mobileNumber" => "",
                     "balance" => \number_format(0, 2, '.', ','),
                  ];
      return $response;
   }

   public function postPayment(Array $postParams): Array
   {

      $response = [
                  'status'=>'FAILED',
                  'receiptNumber'=>'',
                  'error'=>''
               ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);

         $fullURL = $configs['baseURL']."&QueryType=C";
         $fullURL .= "&ApiAuthCode= ".$configs['ApiAuthCode'];
         $fullURL .= "&PaymentMode=".$configs['PaymentMode'];
         $fullURL .= "&AccountNo=D0000000001";
         $fullURL .= "&SalesRefCode=".$postParams['customerAccount'];
         $fullURL .= "&ReceiptAmount=".$postParams['receiptAmount'];
         $fullURL .= "&ExtDbRefNo=".$postParams['transactionId'];

         $apiResponse = Http::timeout($configs['timeout'])
                           ->withHeaders([
                                 'Accept' => '*/*',
                              ])
                           ->get($fullURL);
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['ApiRespCode'] == '1'){
                  $response['status']="SUCCESS";
                  $response['receiptNumber']=$apiResponse['data'][0]['ReceiptNo'];
               }else{
                  throw new Exception(' Mazabuka Billing Client server error. Details: '.$apiResponse['ApiRespMsg'],1);
               }
         } else {
            throw new Exception(" Status code: " . $apiResponse->status(), 2);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" Mazabuka Billing Client server error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   private function getConfigs(string $client_id)
   {

      $configs = [];
      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $configs['baseURL'] = $clientCredentials['POSTPAID_BASE_URL'];
      $configs['ApiAuthCode'] = $clientCredentials['API_AUTH_CODE'];
      $configs['PaymentMode'] = $clientCredentials['PAYMENT_MODE'];
      $configs['timeout'] = $clientCredentials['POSTPAID_TIMEOUT'];
      return $configs;

   }

}
