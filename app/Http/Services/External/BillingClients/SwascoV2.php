<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use Illuminate\Support\Carbon;

use Exception;

class SwascoV2 implements IBillingClient
{

   private $revenuePoints =[
      "BAT"=>"BATOKA",
      "CHI"=>"CHISEKESI",
      "CHK"=>"CHIKANKATA",
      "CHO"=>"CHOMA",
      "CHR"=>"CHIRUNDU",
      "GWE"=>"GWEMBE",
      "ITT"=>"ITEZHITEZHI",
      "KAL"=>"KALOMO",
      "KAZ"=>"KAZUNGULA",
      "LIV"=>"LIVINGSTONE",
      "MAG"=>"MAGOYE",
      "MAZ"=>"MAZABUKA",
      "MAM"=>"MAAMBA",
      "MBL"=>"MBABALA",
      "MUN"=>"MUNYUMBWE",
      "MZE"=>"MONZE",
      "NAM"=>"NAMWALA",
      "NEG"=>"NEGANEGA",
      "PEM"=>"PEMBA",
      "SIA"=>"SIAVONGA",
      "SZE"=>"SINAZEZE",
      "SIN"=>"SINAZONGWE",
      "ZIM"=>"ZIMBA",
   ];
   private $swascoSoapService;
   private string $soapUserName;
   private string $soapPassword;

   public function __construct(
       private BillingCredentialService $billingCredentialsService)
   {}

   public function getAccountDetails(array $params): array{

      $response = [];

      try {

         $this->setConfigs($params['client_id']);

         $getAccountDetailsParams = [ 
                              'username' => $this->soapUserName,
                              'password' => $this->soapPassword,
                              'accountNumber' => $params['customerAccount'],
                           ];

         $apiResponse = $this->swascoSoapService->GetAccountDetails($getAccountDetailsParams);
         $apiResponse = json_decode($apiResponse->return_value,true);
         $apiResponse = $apiResponse['Response'];
         if(is_array($apiResponse)){
            return    [
                           "customerAccount" => $apiResponse['No'],
                           "name" => $apiResponse['Name'],
                           "address" => $apiResponse['Address'],
                           "revenuePoint" => $this->getRevenuePoint(\substr($apiResponse['No'],0,3)),
                           "mobileNumber" => $apiResponse['MobileNo'],
                           "balance" => \number_format((float)$apiResponse['Balance'], 2, '.', ',')
                        ];
         }else{
            throw new Exception("Invalid SWASCO Account Number",1);
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
                                 'phoneNumber' => $postParams['mobileNumber'],
                                 'source' => 1,
                                 'receiptType' => $postParams['receiptType']
                              ];

         $apiResponse = $this->swascoSoapService->PostCustomerReceipt($receiptingParams);
         if($apiResponse->return_value){
            $response['status']="SUCCESS";
            $response['receiptNumber'] = $apiResponse->return_value;
         }else{
            throw new Exception("SWASCO Billing Client PostCustomerReceipt error: ",1);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" SWASCO Billing Client (Post Payment) error. Details: " . $e->getMessage();
         }
      }
      return $response;

   }

   public function changeCustomerDetail(array $postParams): String {

      $response = "";

      try {

         $this->setConfigs($postParams['client_id']);

         $theDate = Carbon::parse($postParams['created_at']);
         $theDate = $theDate->format('Y-m-d');
         $customerParams =  [ 
                              'username' => $this->soapUserName,
                              'password' => $this->soapPassword,
                              'accountNumber' => $postParams['customerAccount'],
                              'mobileNo' => $postParams['newMobileNumber'],
                              'submissionDate' => $theDate,
                              'sourcePhoneNo' => $postParams['mobileNumber']
                           ];

         $apiResponse = $this->swascoSoapService->ChangeCustomerNumber($customerParams);
         if($apiResponse->return_value){
            $response = $apiResponse->return_value;
         }else{
            throw new Exception("SWASCO Billing Client Change Customer Details error: ",1);
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   public function postComplaint(array $postParams): String {
      $response ="";
      try {

         $this->setConfigs($postParams['client_id']);
         $theDate = Carbon::parse($postParams['created_at']);
         $theDate = $theDate->format('Y-m-d');
         $receiptingParams =  [ 
                                 'username' => $this->soapUserName,
                                 'password' => $this->soapPassword,
                                 'accountNo' => $postParams['customerAccount'],
                                 'compaintCode' => $postParams['complaintCode'],
                                 'submissionDate' => $theDate,
                                 'sourcePhoneNo' => $postParams['mobileNumber'] 
                              ];

         $apiResponse = $this->swascoSoapService->SubmitFaultComplaint($receiptingParams);

         if($apiResponse->return_value){
            $response = $apiResponse->return_value;
         }else{
            throw new Exception("SWASCO Billing Client SubmitFaultComplaint error: ",1);
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   private function setConfigs(string $client_id){

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $baseURL = $clientCredentials['SWASCOV2_SOAP_BASE_URL'];
      $wsdlPath = $baseURL;//.$clientCredentials['wsdl_URI'];
      $soapOptions =  [
                           'exceptions' => true,
                           'login' => $clientCredentials['SWASCOV2_SOAP_USERNAME'],
                           'password' => $clientCredentials['SWASCOV2_SOAP_PASSWORD'],
                           'cache_wsdl' => WSDL_CACHE_NONE,
                           'soap_version' => SOAP_1_1,
                           'trace' => 1,
                           'connection_timeout' => $clientCredentials['SOAP_CONNECTION_TIMEOUT']
                        ];
      $this->swascoSoapService = new \SoapClient($wsdlPath,$soapOptions);
      $this->swascoSoapService->__setLocation($baseURL);
      $this->soapUserName =$clientCredentials['SWASCOV2_SOAP_USERNAME'];
      $this->soapPassword = $clientCredentials['SWASCOV2_SOAP_PASSWORD'];

   }

   public function getRevenuePoint(String $code): string
   {
      
      if(\array_key_exists($code,$this->revenuePoints)){
         return $this->revenuePoints[$code];
      }else{
         return "OTHER";
      }
      
   }

   public function _postReconnection(Array $postParams): array 
   {

      $response = [
                        'status'=>'FAILED',
                        'receiptNumber'=>'',
                        'error'=>''
                     ];

      try {

         $this->setConfigs($postParams['client_id']);

         $theDate = Carbon::parse($postParams['created_at']);
         $theDate = $theDate->format('Y-m-d');

         $receiptingParams =  [ 
                                 'referenceNumber' => $postParams['referenceNumber'],
                                 'accountNumber' => $postParams['account'],
                                 'sourcePhoneNumber' => $postParams['mobileNumber'],
                                 'paymentType' => $postParams['paymentType'],
                                 'creditAmount' => $postParams['amount'],
                                 'paymentDate' => $theDate,
                                 'source' => 1,
                                 'receiptType' => $postParams['receiptType'],
                                 'username' => $this->soapUserName,
                                 'password' => $this->soapPassword,
                              ];

         $apiResponse = $this->swascoSoapService->PostReconnectionPayment($receiptingParams);
         $apiResponse = json_decode($apiResponse->return_value,true);
         $apiResponse = $apiResponse['RESPONSE'];
         if(is_array($apiResponse) && key_exists('RECEIPTNO',$apiResponse)){
            $response['status']="SUCCESS";
            $response['receiptNumber'] = $apiResponse['RECEIPTNO'];
         }else{
            throw new Exception("SWASCO Billing Client Post Reconnection Payment error: ",1);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" SWASCO Billing Client Post Reconnection Payment error. Details: " . $e->getMessage();
         }
      }

      return $response;

   }

   public function _postVacuumTanker(Array $postParams): array 
   {

      $response = [
                        'status'=>'FAILED',
                        'receiptNumber'=>'',
                        'error'=>''
                     ];

      try {

         $this->setConfigs($postParams['client_id']);

         $theDate = Carbon::parse($postParams['created_at']);
         $theDate = $theDate->format('Y-m-d');

         $receiptingParams =  [ 
                                 'referenceNumber' => $postParams['referenceNumber'],
                                 'accountNumber' => $postParams['account'],
                                 'sourcePhoneNumber' => $postParams['mobileNumber'],
                                 'paymentType' => $postParams['paymentType'],
                                 'creditAmount' => $postParams['amount'],
                                 'paymentDate' => $theDate,
                                 'source' => 1,
                                 'receiptType' => $postParams['receiptType'],
                                 'username' => $this->soapUserName,
                                 'password' => $this->soapPassword,
                              ];
           
         $apiResponse = $this->swascoSoapService->PostVacuumTankerPayment($receiptingParams);
         $apiResponse = json_decode($apiResponse->return_value,true);
         $apiResponse = $apiResponse['RESPONSE'];
         if(is_array($apiResponse) && key_exists('RECEIPTNO',$apiResponse)){
            $response['status']="SUCCESS";
            $response['receiptNumber'] = $apiResponse['RECEIPTNO'];
         }else{
            throw new Exception("SWASCO Billing Client Post Reconnection Payment error: ",1);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" SWASCO Billing Client Post Reconnection Payment error. Details: " . $e->getMessage();
         }
      }

      return $response;

   }

   public function _postNewConnection(Array $postParams): array 
   {

      $response = [
                        'status'=>'FAILED',
                        'receiptNumber'=>'',
                        'error'=>''
                     ];

      try {

         $this->setConfigs($postParams['client_id']);

         $theDate = Carbon::parse($postParams['created_at']);
         $theDate = $theDate->format('Y-m-d');

         $receiptingParams =  [ 
                                 'referenceNumber' => $postParams['referenceNumber'],
                                 'accountNumber' => $postParams['customerAccount'],
                                 'sourcePhoneNumber' => $postParams['mobileNumber'],
                                 'paymentType' => $postParams['paymentType'],
                                 'creditAmount' => $postParams['amount'],
                                 'paymentDate' => $theDate,
                                 'username' => $this->soapUserName,
                                 'password' => $this->soapPassword,
                              ];

         $apiResponse = $this->swascoSoapService->PostVacuumTankerPayment($receiptingParams);
         $apiResponse = json_decode($apiResponse->return_value,true);
         $apiResponse = $apiResponse['RESPONSE'];
         if(is_array($apiResponse) && key_exists('RECEIPTNO',$apiResponse)){
            $response['status']="SUCCESS";
            $response['receiptNumber'] = $apiResponse['RECEIPTNO'];
            return  $response;
         }else{
            throw new Exception("SWASCO Billing Client Post Reconnection Payment error: ",1);
         }

      } catch (\Throwable $e) {
         if ($e->getCode() == 1) {
            $response['error']=$e->getMessage();
         } else{
            $response['error']=" SWASCO Billing Client Post Reconnection Payment error. Details: " . $e->getMessage();
         }
      }

      return $response;

   }

}
