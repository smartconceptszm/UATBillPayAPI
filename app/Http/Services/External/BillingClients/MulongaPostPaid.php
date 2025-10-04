<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Carbon;

use Exception;

class MulongaPostPaid implements IBillingClient
{

   private $revenuePoints =[
      "11"=>"CHINGOLA",
      "22"=>"MUFULIRA",
      "30"=>"CHILILABOMBWE",
   ];
   private $soapService;
   private string $soapUserName;
   private string $soapPassword;

   public function __construct(
       private BillingCredentialService $billingCredentialsService)
   {}

   public function getAccountDetails(array $params): array{

      $response = [];

      try {

         if(!(\strlen($params['customerAccount'])==10)){
            throw new Exception("Invalid SWASCo account number",1);
         }

         $this->setConfigs($params['client_id']);

         $getAccountDetailsParams = [ 
                              'username' => $this->soapUserName,
                              'password' => $this->soapPassword,
                              'customerNumber' => $params['customerAccount'],
                           ];

         $apiResponse = $this->soapService->GetCustomerDetails($getAccountDetailsParams);

         $apiResponse = json_decode($apiResponse->return_value,true);
         $apiResponse = $apiResponse['Response'];
         if(is_array($apiResponse)){
            return    [
                           "customerAccount" => $apiResponse['No'],
                           "name" => $apiResponse['Name'],
                           "address" => $apiResponse['Address'],
                           "revenuePoint" => $this->getRevenuePoint(\substr($apiResponse['No'],0,2)),
                           "composite" =>'ORDINARY',
                           "consumerTier" => '',
                           "consumerType" => '',
                           "mobileNumber" => $apiResponse['MobileNo'],
                           "balance" => \number_format((float)\str_replace(",", "", $apiResponse['Balance']), 2, '.', ',')
                        ];
         }else{
            throw new Exception("Invalid MULONGA Account Number",1);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            throw $e;
         }else{
            throw new Exception("Error executing 'Get Account Details' Soap Request. Details: " . $e->getMessage(), 2);
         }
      }

      return $response;

   }

   public function postPayment(Array $postParams): array 
   {

      $response=[
            'status'=>'FAILED',
            'receiptNumber'=>'',
            'error'=>''
         ];

      try {
         $this->setConfigs($postParams['client_id']);
         $receiptingParams =  [ 
                                 'username' => $this->soapUserName,
                                 'password' => $this->soapPassword,
                                 'referenceNumber' => $postParams['referenceNumber'],
                                 'accountNumber' => $postParams['account'],
                                 'lineAmount' => $postParams['amount'],
                                 'paymentType' => $postParams['paymentType'],
                                 'description' => $postParams['description'],
                                 'phoneNumber' => $postParams['mobileNumber'],
                                 'receiptType' => $postParams['receiptType']
                              ];

         $apiResponse = $this->soapService->PostCustomerReceiptUSSD($receiptingParams);
         if($apiResponse->return_value){
            $response['status']="SUCCESS";
            $response['receiptNumber'] = $apiResponse->return_value;
         }else{
            throw new Exception("MULONGA Billing Client PostCustomerReceipt error: ",1);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" MULONGA Billing Client (Post Payment) error. Details: " . $e->getMessage();
         }
      }
      return $response;

   }

   private function setConfigs(string $client_id){

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $baseURL = $clientCredentials['POSTPAID_SOAP_BASE_URL'];
      $wsdlPath = $baseURL;//.$clientCredentials['wsdl_URI'];
      $soapOptions =  [
                           'exceptions' => true,
                           'login' => $clientCredentials['POSTPAID_SOAP_USERNAME'],
                           'password' => $clientCredentials['POSTPAID_SOAP_PASSWORD'],
                           'cache_wsdl' => WSDL_CACHE_BOTH,
                           'soap_version' => SOAP_1_1,
                           'trace' => 1,
                           'connection_timeout' => $clientCredentials['SOAP_CONNECTION_TIMEOUT']
                        ];

      $this->soapService = new \SoapClient($wsdlPath,$soapOptions);

      $this->soapService->__setLocation($baseURL);
      $this->soapUserName =$clientCredentials['POSTPAID_SOAP_USERNAME'];
      $this->soapPassword = $clientCredentials['POSTPAID_SOAP_PASSWORD'];

   }

   public function getRevenuePoint(String $code): string
   {
      
      if(\array_key_exists($code,$this->revenuePoints)){
         return $this->revenuePoints[$code];
      }else{
         return "OTHER";
      }
      
   }

}
