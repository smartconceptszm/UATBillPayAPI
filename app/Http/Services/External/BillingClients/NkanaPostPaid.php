<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Web\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;
use Exception;

class NkanaPostPaid implements IBillingClient
{

   private string $AuthenticationCode;
   private string $baseURL;

   public function __construct(private BillingCredentialService $billingCredentialsService)
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {

         $this->getConfigs($params['client_id']);
         $fullURL = $this->baseURL."nwsc-api/ClientDetails/Customer_Details";
         $apiResponse = Http::withHeaders([
                              'Accept' => '/',
                              'AuthenticationCode'=> $this->AuthenticationCode
                           ])->get($fullURL, ["customerID"=> $params['accountNumber']]);
         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            if($apiResponse['MsgStatusCode'] == 'ENQ001'){
               $response['accountNumber'] = $params['accountNumber'];
               $response['name'] =   $apiResponse['Cus_Details']['0']['INITIAL']."".$apiResponse['Cus_Details']['0']['SURNAME'];
               $response['address'] = $apiResponse['Cus_Details']['0']['UA_ADRESS1'];
               $response['district'] = "OTHER";
               $response['mobileNumber'] =  $apiResponse['Cus_Details']['0']['CELL_TEL_NO'];
               $response['balance'] = $apiResponse['Cus_Details']['0']['Closing_Balance'];
            }else{
               if($apiResponse['MsgStatusCode'] == 'Enq005'){
                  //throw new Exception($apiResponse['statusNarration'], 1);
                  throw new Exception("Invalid Nkana POST-PAID Account Number", 1);
               }else{
                  throw new Exception($apiResponse['statusNarration'], 2);
               }
            }
         } else {
            throw new Exception("status code: " . $apiResponse->status(), 2);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            throw new Exception($e->getMessage(), 1);
         } else {
            throw new Exception("NKANA Remote Service responded with: " . $e->getMessage(), 2);
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
         $this->getConfigs($postParams['client_id']);
         $fullURL = $this->baseURL."nwsc-api/Payment/ClientPayment";
         $apiResponse = Http::withHeaders([
                                 'Accept' => '/',
                                 'AuthenticationCode'=> $this->AuthenticationCode
                              ])->post($fullURL, $postParams);
         if ($apiResponse->status() == 201) {
               $apiResponse = $apiResponse->json();
               if($apiResponse['Trax_Code'] == 'CPM-003'){
                  $response['status']="SUCCESS";
                  $response['receiptNumber']=$apiResponse['ClientPayment_gen']['cp_refNumber'];
               }else{
                  throw new Exception(' NKANA Billing Client server error. Details: '.
                                       $apiResponse['Trax_Code'] == 'CPM-003'.$apiResponse['ClientPayment_gen']['cpPaymentStatus'],1);
               }
         } else {
            if ($apiResponse->status() >= 400) {
               throw new Exception(' NKANA Billing Client server error. Status code: '.$apiResponse->status(),1);
            } else {
               throw new Exception(" Status code: " . $apiResponse->status(), 2);
            }
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
               $response['error']=$e->getMessage();
         } else{
               $response['error']=" NKANA Billing Client server error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   private function getConfigs(string $client_id)
   {
      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $this->AuthenticationCode = $clientCredentials['AuthenticationCode'];
      $this->baseURL = $clientCredentials['POSTPAID_BASE_URL'];
   }


}