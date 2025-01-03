<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;
use Exception;

class MazabukaOnCustomerAccount implements IBillingClient
{

   public function __construct(private BillingCredentialService $billingCredentialsService)
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {

         $configs = $this->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL']."&QueryType=S";
         $fullURL .= "&ApiAuthCode= ".$configs['ApiAuthCode'];
         $fullURL .= "&PaymentMode=".$configs['PaymentMode'];
         $fullURL .= "&AccountNo=".$params['customerAccount'];
         $fullURL .= "&SalesRefCode='2022000000'";
         $fullURL .= "&ReceiptAmount=0&ExtDbRefNo=''";

         $apiResponse = Http::withHeaders([
                                 'Accept' => '*/*',
                              ])
                           ->get($fullURL);

         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if(is_array($apiResponse) && key_exists('Id',$apiResponse)){
               $response['customerAccount'] = $apiResponse['Id'];
               $response['name'] = $apiResponse['AccountName'];
               $response['address'] = $apiResponse['ContactAddress'];
               $response['revenuePoint'] = "OTHER";
               $response['mobileNumber'] =  $apiResponse['MobilePhone'];
               $response['balance'] = $apiResponse['BalanceNow'];
            }else{
               throw new Exception("Invalid Mazabuka POST-PAID Account Number", 1);
            }
         } else {
            throw new Exception("status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            throw new Exception($e->getMessage(), 1);
         } else {
            throw new Exception("Mzabuka Remote Service responded with: " . $e->getMessage(), 2);
         }
      }

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
         $fullURL = $configs['baseURL']."&QueryType=R";
         $fullURL .= "&ApiAuthCode= ".$configs['ApiAuthCode'];
         $fullURL .= "&PaymentMode=".$configs['PaymentMode'];
         $fullURL .= "&AccountNo=".$postParams['customerAccount'];
         $fullURL .= "&SalesRefCode=''";
         $fullURL .= "&ReceiptAmount=".$postParams['receiptAmount'];
         $fullURL .= "&ExtDbRefNo=".$postParams['transactionId'];

         $apiResponse = Http::withHeaders([
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
      return $configs;

   }


}