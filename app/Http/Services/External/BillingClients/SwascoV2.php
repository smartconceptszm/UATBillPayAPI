<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Utility\XMLtoArrayParser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

use Exception;

class SwascoV2 implements IBillingClient
{

   private $districts =[
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
   private string $soapToken;
   private string $cashierNo;
   private string $operator;
   private $cacheTTL;

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
                           "district" => $this->getDistrict(\substr($apiResponse['No'],0,3)),
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

         $this->setConfigs($postParams['client_id'],$postParams['providerName']);
         $receiptingParams =  [ 
                                 'username' => $this->soapUserName,
                                 'password' => $this->soapPassword,
                                 'accountNumber' => $postParams['customerAccount'],
                                 'lineAmount' => $postParams['amount'],
                                 'referenceNumber' => $postParams['reference'],
                                 'paymentType' => '01',
                                 'phoneNumber' => $postParams['mobileNumber'] 
                              ];

         $apiResponse = $this->swascoSoapService->PostCustomerReceipt($receiptingParams);
         if($apiResponse->return_value){
            $response['status']="SUCCESS";
            $response['receiptNumber'] = $apiResponse->return_value;
            return  $response;
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


   public function postComplaint(array $postParams): String {
      $response ="";
      try {

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   public function changeCustomerDetail(array $postParams): String {

      $response = "";

      try {

         $this->setConfigs($postParams['client_id']);

         $customerParams =  [ 
                              'username' => $this->soapUserName,
                              'password' => $this->soapPassword,
                              'accountNumber' => $postParams['customerAccount'],
                              'mobileNo' => $postParams['newMobileNumber'],
                              'submissionDate' => $postParams['created_at'],
                              'sourcePhoneNo' => $postParams['mobileNumber']
                           ];
         $apiResponse = $this->swascoSoapService->ChangeCustomerNumber($customerParams);
         if($apiResponse->return_value){
            return  $apiResponse->return_value;
         }else{
            throw new Exception("SWASCO Billing Client PostCustomerReceipt error: ",1);
         }


      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }


   private function setConfigs(string $client_id,string $providerName=null){

      $clientCredentials = $this->billingCredentialsService->getClientCredentials($client_id);
      $baseURL = $clientCredentials['SOAP_BASE_URL'];
      $wsdlPath = $baseURL;//.$clientCredentials['wsdl_URI'];
      $soapOptions =  [
                           'exceptions' => true,
                           'login' => $clientCredentials['SOAP_USERNAME'],
                           'password' => $clientCredentials['SOAP_PASSWORD'],
                           'cache_wsdl' => WSDL_CACHE_BOTH,
                           'soap_version' => SOAP_1_1,
                           'trace' => 1,
                           'connection_timeout' => $clientCredentials['SOAP_CONNECTION_TIMEOUT']
                        ];
      $this->swascoSoapService = new \SoapClient($wsdlPath,$soapOptions);
      $this->swascoSoapService->__setLocation($baseURL);
      // if($providerName){
      //    $this->cashierNo = $clientCredentials['SOAP_CASHIER_NO_'.$providerName];
      // }
      $this->soapUserName =$clientCredentials['SOAP_USERNAME'];
      $this->soapPassword = $clientCredentials['SOAP_PASSWORD'];
      // $this->operator = $clientCredentials['SOAP_OPERATOR'];
      // $this->cacheTTL = $clientCredentials['BALANCE_CACHE'];

   }


   public function getDistrict(String $code): string
   {
      
      if(\array_key_exists($code,$this->districts)){
         return $this->districts[$code];
      }else{
         return "OTHER";
      }
      
   }

}
