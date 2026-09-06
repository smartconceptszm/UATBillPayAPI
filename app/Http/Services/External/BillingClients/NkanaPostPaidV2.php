<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Facades\Http;
use Exception;

class NkanaPostPaidV2 implements IBillingClient
{

   public function __construct(private BillingCredentialService $billingCredentialsService)
   {}

   public function getAccountDetails(array $params): array
   {

      $response = [];
      try {

         $configs = $this->getConfigs($params['client_id']);
         $fullURL = $configs['baseURL']."nwsc-api/v2/Transaction/CustomerDetails";
         
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' => '/',
                                    'AuthenticationCode'=> $configs['AuthenticationCode']
                                 ])
                              ->get($fullURL, ["customerId"=> $params['customerAccount']]);

         $body = $apiResponse->json();

         if (!is_array($body)) {
               throw new Exception(
                  ' NKANA Billing Client V2 error. Details: Invalid response (HTTP '
                  . $apiResponse->status() . '): ' . $apiResponse->body(),
                  1
               );
         }
         
         $statusCode = $body['statusCode'] ?? null;
         $message    = $body['message'] ?? 'No message returned';

         if ($apiResponse->status() == 200 && $statusCode == 'OT001') {
               $response['customerAccount'] = $params['customerAccount'];
               $response['name'] =   $body['cusDetails']['inital']." ".$body['cusDetails']['surname'];
               $response['address'] = $body['cusDetails']['uaAdress1'];
               $response['composite'] = 'ORDINARY';
               $response['revenuePoint'] = "OTHER";
               $response['consumerTier'] = '';
               $response['consumerType'] = '';
               $response['mobileNumber'] =  $body['cusDetails']['cellTelNo'];
               $response['balance'] = $body['cusDetails']['closingBalance'];
         } else {
            throw new Exception(
               ' NKANA Billing Client V2 error. Details: '
               . ($statusCode ?? 'UNKNOWN') . ' ' . $message
               . ' (HTTP ' . $apiResponse->status() . ')',
               1
            );
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
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseURL']."nwsc-api/v2/Transaction/Payments";
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' =>  '*/*',
                                    'AuthenticationCode'=> $configs['AuthenticationCode']
                                 ])
                              ->post($fullURL, $postParams);

         $body = $apiResponse->json();

         if (!is_array($body)) {
               throw new Exception(
                  ' NKANA Billing Client V2 error. Details: Invalid response (HTTP '
                  . $apiResponse->status() . '): ' . $apiResponse->body(),
                  1
               );
         }
         
         $statusCode = $body['statusCode'] ?? null;
         $message    = $body['message'] ?? 'No message returned';

         if ($apiResponse->status() == 200 && $statusCode == 'OT001') {
            $response['status'] = "SUCCESS";
            $response['receiptNumber'] = $body['clientPaymentgen']['refNumber'] ?? '';
         } else {
            throw new Exception(
               ' NKANA Billing Client V2 error. Details: '
               . ($statusCode ?? 'UNKNOWN') . ' ' . $message
               . ' (HTTP ' . $apiResponse->status() . ')',
               1
            );
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" NKANA Billing Client V2 error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   public function postOtherPayment(array $postParams): array
   {

      $response = [
                  'status'=>'FAILED',
                  'receiptNumber'=>'',
                  'error'=>''
               ];

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseURL']."nwsc-api/v2/Transaction/ServiceConnection";
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' =>  '*/*',
                                    'AuthenticationCode'=> $configs['AuthenticationCode']
                                 ])
                              ->post($fullURL, $postParams);

         $body = $apiResponse->json();

         if (!is_array($body)) {
               throw new Exception(
                  ' NKANA Billing Client V2 error. Details: Invalid response (HTTP '
                  . $apiResponse->status() . '): ' . $apiResponse->body(),
                  1
               );
         }
         
         $statusCode = $body['statusCode'] ?? null;
         $message    = $body['message'] ?? 'No message returned';

         if ($apiResponse->status() == 200 && $statusCode == 'OT001') {
            $response['status'] = "SUCCESS";
            $response['receiptNumber'] = $body['clientPaymentgen']['refNumber'] ?? '';
         } else {
            throw new Exception(
               ' NKANA Billing Client V2 error. Details: '
               . ($statusCode ?? 'UNKNOWN') . ' ' . $message
               . ' (HTTP ' . $apiResponse->status() . ')',
               1
            );
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" NKANA Billing Client V2 error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   public function postComplaint(array $postParams): string
   {

      $response = "";

      try {
         $configs = $this->getConfigs($postParams['client_id']);
         $fullURL = $configs['baseURL']."nwsc-api/v2/Transaction/ComplaintsRegistration";
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Accept' =>  '*/*',
                                    'AuthenticationCode'=> $configs['AuthenticationCode']
                                 ])
                              ->post($fullURL, $postParams);

         $body = $apiResponse->json();

         if (!is_array($body)) {
               throw new Exception(
                  ' NKANA Billing Client V2 error. Details: Invalid response (HTTP '
                  . $apiResponse->status() . '): ' . $apiResponse->body(),
                  1
               );
         }
         
         $statusCode = $body['statusCode'] ?? null;
         $message    = $body['message'] ?? 'No message returned';

         if ($apiResponse->status() == 200 && $statusCode == 'OT001') {
            $response = $body['complaintNum']['complaintNum'];
         } else {
            throw new Exception(
               ' NKANA Billing Client V2 error. Details: '
               . ($statusCode ?? 'UNKNOWN') . ' ' . $message
               . ' (HTTP ' . $apiResponse->status() . ')',
               1
            );
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" NKANA Billing Client V2 error. Details: " . $e->getMessage();
         }
      }
      return $response;
   }

   private function getConfigs(string $client_id):array
   {

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $configs['AuthenticationCode'] = "Basic ".$clientCredentials['AuthenticationCode'];
      $configs['baseURL'] = $clientCredentials['POSTPAID_BASE_URL_V2'];
      $configs['timeout'] = $clientCredentials['POSTPAID_TIMEOUT'];
      return $configs;

   }


}