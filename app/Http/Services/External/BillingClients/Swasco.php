<?php

namespace App\Http\Services\External\BillingClients;

use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Http;

use Exception;

class Swasco implements IBillingClient
{
    
   private $paymentTypes = [
      "1" => [
         "code" => "4",
         "name" => "Reconnection",
         "accountType" => "Customer",
         "accountNo" => "320001",
         "hasApplicationNo"=>false
      ],
      "2" => [
         "code" => "12",
         "name" => "Vacuum Tanker Pit Emptying",
         "accountType" => "G/L Account",
         "accountNo" => "320008",
         "hasApplicationNo"=>false
      ],
      // "3" => [
      //     "code" => "5",
      //     "name" => "New Water Connection",
      //     "accountType" => "G/L Account",
      //     "accountNo" => "320003",
      //     "hasApplicationNo"=>true
      // ],
      // "4" => [
      //     "code" => "8",
      //     "name" => "Capital Contribution",
      //     "accountType" => "G/L Account",
      //     "accountNo" => "222535",
      //     "hasApplicationNo"=>true
      // ],
      // "5" => [
      //     "code" => "6",
      //     "name" => "Sewerage Connection",
      //     "accountType" => "G/L Account",
      //     "accountNo" => "320004",
      //     "hasApplicationNo"=>true
      // ],
      // "6" => [
      //     "code" => "13",
      //     "name" => "Unblocking Sewer",
      //     "accountType" => "G/L Account",
      //     "accountNo" => "320008",
      //     "hasApplicationNo"=>false
      // ],
      // "7" => [
      //     "code" => "17",
      //     "name" => "Meter Testing",
      //     "accountType" => "G/L Account",
      //     "accountNo" => "320099",
      //     "hasApplicationNo"=>false
      // ],
      // "8" => [
      //     "code" => "21",
      //     "name" => "Bowser Water",
      //     "accountType" => "G/L Account",
      //     "accountNo" => "320007",
      //     "hasApplicationNo"=>false
      // ],
   ];

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

   private $customerDetails = [
      "1" => [
         "name" => "Mobile No.",
         "type" => "mobile",
         "format" => "095xxxxxxx"
      ],
      "2" => [
         "name" => "House Number",
         "type" => "",
         "format" => ""
      ],
      "3" => [
         "name" => "Street Name",
         "type" => "",
         "format" => ""
      ],
      "4" => [
         "name" => "Area or Location",
         "type" => "",
         "format" => ""
      ],
   ];
   private int $swascoReceiptingTimeout;
   private int $swascoTimeout;
   private string $baseURL;

   public function __construct()
   {
      $this->swascoReceiptingTimeout = \intval(\env('SWASCO_RECEIPTING_TIMEOUT'));
      $this->swascoTimeout = \intval(\env('SWASCO_REMOTE_TIMEOUT'));
      $this->baseURL = \env('SWASCO_BASE_URL');
   }

   public function getAccountDetails(array $params): array
   {

      $response = [];

      try {

         if(!(\strlen($params['accountNumber'])==10)){
            throw new Exception("Invalid SWASCo account number",1);
         }

         $fullURL = $this->baseURL . "navision/customers/".\rawurlencode($params['accountNumber']);
         $apiResponse = Http::timeout($this->swascoTimeout)->withHeaders([
                                             'Content-Type' => 'application/json',
                                             'Accept' => '*/*'
                                       ])->get($fullURL);

         if ($apiResponse->status() == 200) {
            $apiResponse = $apiResponse->json();
            $response = $apiResponse['data']['customer'];
            $response['district']=$this->getDistrict(\substr($response['accountNumber'],0,3));
         } else {
            if ($apiResponse->status() == 404 || $apiResponse->status() == 500) {
               $apiResponse = $apiResponse->json();
               if ($apiResponse['status']['code'] == 404) {
                  throw new Exception("Invalid SWASCo account number", 1);
               } else {
                  throw new Exception($apiResponse['status']['message'], 2);
               }
            } else {
               throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 2);
            }
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1 || $e->getCode() == 2) {
            throw $e;
         } else {
            throw new Exception("Error executing 'Get Account Details': " . $e->getMessage(), 3);
         }
      }

      return $response;
   }

   public function postPayment(Array $postParams): Array 
   {

      $response=[
               'status'=>'FAILED',
               'receiptNumber'=>'',
               'error'=>''
         ];

      try {
         switch ($postParams['paymentType']) {
               case '1':
                  $fullURL = $this->baseURL . "navision/payments/bills";
                  break;
               case '4':
                  $fullURL = $this->baseURL . "navision/payments/reconnections";
                  break;
               case '5':
                  $fullURL = $this->baseURL . "navision/payments/waterconnections";
                  break;                    
               case '6':
                  $fullURL = $this->baseURL . "navision/payments/sewerconnections";
                  break;
               case '8':
                  $fullURL = $this->baseURL . "navision/payments/capitalcontributions";
                  break;
               case '12':
                  $fullURL = $this->baseURL . "navision/payments/vacuumtankers";
                  break;                                      
               default:
                  $fullURL = $this->baseURL . "navision/payments/bills";
                  break;
         }
         
         $apiResponse = Http::timeout($this->swascoReceiptingTimeout)
               ->withHeaders([
                  'Content-Type' => 'application/json',
                  'Accept' => '*/*',
               ])
               ->post($fullURL, [
                  'accountNumber' => $postParams['account'],
                  'amount' => (string)$postParams['amount'],
                  "mobileNumber" => $postParams['mobileNumber'],
                  "referenceNumber" => $postParams['referenceNumber'],
                  "paymentType" => $postParams['paymentType']
               ]);
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               $response['status']="SUCCESS";
               $response['receiptNumber']=$apiResponse['data']['ReceiptNo'];
         } else {
               if ($apiResponse->status() == 500) {
                  $apiResponse = $apiResponse->json();
                  throw new Exception($apiResponse['status']['message']);
               } else {
                  throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 1);
               }
         }
      } catch (\Throwable $e) {
         $response['error']=$e->getMessage();
      }
      return $response;
   }

   public function postComplaint(array $postParams): String {
      $response ="";
      try {
         $fullURL = $this->baseURL . "navision/complaints";
         $apiResponse = Http::timeout($this->swascoTimeout)
               ->withHeaders([
                  'Content-Type' => 'application/json',
                  'Accept' => '*/*',
               ])
               ->post($fullURL, [
                  'accountNumber' => $postParams['accountNumber'],
                  'complaintCode' => $postParams['complaintCode'],
                  "mobileNumber" => $postParams['mobileNumber']
               ]);

         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               $response = $apiResponse['data']['referenceNo'];
         } else {
               if ($apiResponse->status() == 500) {
                  $apiResponse = $apiResponse->json();
                  throw new Exception($apiResponse['status']['message']);
               } else {
                  throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 1);
               }
         }
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
   }

   public function changeCustomerDetail(array $postParams): String {

      $response = "";
      try {


         //$response = 'CASE2929292929';

         $fullURL = $this->baseURL . "navision/mobilenos";
         $apiResponse = Http::timeout($this->swascoTimeout)
               ->withHeaders([
                  'Content-Type' => 'application/json',
                  'Accept' => '*/*',
               ])
               ->post($fullURL, [
                  'accountNumber' => $postParams['accountNumber'],
                  'mobileNumber' => $postParams['mobileNumber'],
                  'newMobileNumber' => $postParams['newMobileNumber'],
               ]);
         if ($apiResponse->status() == 200) {
               $apiResponse = $apiResponse->json();
               $response = $apiResponse['data']['referenceNo'];
         } else {
               if ($apiResponse->status() == 500) {
                  $apiResponse = $apiResponse->json();
                  throw new Exception($apiResponse['status']['message']);
               } else {
                  throw new Exception("SWASCO Remote Service responded with status code: " . $apiResponse->status(), 1);
               }
         }

      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      return $response;
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
