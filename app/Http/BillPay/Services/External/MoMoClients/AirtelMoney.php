<?php

namespace App\Http\BillPay\Services\External\MoMoClients;

use App\Http\BillPay\Services\External\MoMoClients\IMoMoClient;
use Illuminate\Support\Facades\Http;
use Exception;

class AirtelMoney implements IMoMoClient
{

   public function requestPayment(object $dto):object
   {

      $mainResponse = [
            'status' => 'SUBMISSION FAILED',
            'transactionId' => '',
            'error' => ''
         ];

      try {
         $mainResponse['transactionId'] = $this->getTransactionId($dto);
         $configs = $this->getConfigs($dto->urlPrefix);
         $apiToken = $this->getToken($configs);
         $token = $apiToken['access_token'];

         $fullURL = $configs['baseURL'] . "merchant/v1/payments/";
         $airtelResponse = Http::timeout($configs['timeout'])->withHeaders([
               "Content-Type" => "application/json",
               "Accept" => "*/*",
               "X-Country" => "ZM",
               "X-Currency" => "ZMW",
               "Authorization" => "Bearer " . $token
            ])->post($fullURL, [
                  "reference" => $configs['tx_reference'],
                  "subscriber" => [
                     "country" => "ZM",
                     "currency" => "ZMW",
                     "msisdn" => \substr($dto->mobileNumber,3,9),
                  ],
                  "transaction" => [
                     "amount" => $dto->paymentAmount,
                     "country" => "ZM",
                     "currency" => "ZMW",
                     "id" => $mainResponse['transactionId']
                  ]
            ]);

         if ($airtelResponse->status() >= 200 && $airtelResponse->status() < 300) {
            $airtelResponse = $airtelResponse->json();
            if (\array_key_exists('status', $airtelResponse)) {
               if ($airtelResponse['status']['code'] === "200"){
                  if (\array_key_exists('data', $airtelResponse)) {
                        if (\array_key_exists('transaction', $airtelResponse['data'])) {
                           if (\array_key_exists('status', $airtelResponse['data']['transaction'])) {
                              if($airtelResponse['data']['transaction']['status'] == "Success."){
                                    $mainResponse['status'] = "SUCCESS";
                              }else{
                                    if (\array_key_exists('message', $airtelResponse['data']['transaction'])) {
                                       $errorMessage = "Error on collect funds. Airtel response: ".$airtelResponse['data']['transaction']['message'].".";
                                    }else{
                                       $errorMessage = "Error on collect funds. Airtel response: ".$airtelResponse['data']['transaction']['status'].".";
                                    }
                                    throw new Exception($errorMessage, 2);
                              }
                           }
                        }
                  }
                  if($mainResponse['status'] != "SUCCESS"){
                        throw new Exception("Error on collect funds. Response data not available.", 2);
                  }
               }else{
                  $errorMessage = "Error on collect funds. Airtel responded with Status Code ".$airtelResponse['status']['code'].".";
                  throw new Exception($errorMessage, 2);
               }
            }else{
               throw new Exception("Error on collect funds. Response status details not available.", 2);
            }
         } else {
            throw new Exception("Error on collect funds. Airtel Money responded with Status Code " . 
                                    $airtelResponse->status().".", 2);
         }
      } catch (\Throwable $e) {
         if ($e->getCode() == 1 || $e->getCode() == 2) {
               $mainResponse['error'] = $e->getMessage();
         } else {
               $mainResponse['error'] = "Error on collect funds. Airtel Money response details: " . 
                                             $e->getMessage();
         }
      }
      
      $dto->mnoResponse =  $mainResponse;
      return $dto;
   }

   public function confirmPayment(object $dto):object
   {

      $response = [
         "status" => "FAILED",
         "mnoTransactionId" => "",
         "error" => ""
      ];

      try {
         $configs = $this->getConfigs($dto->urlPrefix);
         $apiToken = $this->getToken($configs);
         $token = $apiToken['access_token'];
         $fullURL = $configs['baseURL'] . "standard/v1/payments/".$dto->transactionId;
         $apiResponse = Http::timeout($configs['timeout'])->withHeaders([
               "Content-Type" => "application/json",
               "Accept" => "*/*",
               "X-Country" => "ZM",
               "X-Currency" => "ZMW",
               "Authorization" => "Bearer ".$token
         ])->get($fullURL);
         if ($apiResponse->status() >= 200 && $apiResponse->status() < 300) {
               $apiResponse = $apiResponse->json();
               if (\array_key_exists('status', $apiResponse)) {
                  if ($apiResponse['status']['code'] === "200"){
                     if (\array_key_exists('data', $apiResponse)) {
                           if (\array_key_exists('transaction', $apiResponse['data'])) {
                              if (\array_key_exists('status', $apiResponse['data']['transaction'])) {
                                 if($apiResponse['data']['transaction']['status'] == "TS"){
                                       $response['status'] = "PAID";
                                       $response['mnoTransactionId'] = $apiResponse['data']['transaction']['airtel_money_id'];
                                 }else{
                                       if (\array_key_exists('message', $apiResponse['data']['transaction'])) {
                                          $errorMessage = "Airtel response: ".$apiResponse['data']['transaction']['message'];
                                          if(\strlen($errorMessage)>110){
                                             $errorMessage = "Airtel response: Customer has insufficient funds (or entered incorrect PIN) to complete this transaction.";
                                          }
                                       }else{
                                          $errorMessage = "Error on get transaction status. Airtel response: ".$apiResponse["data"]["transaction"]["status"].".";
                                       }
                                       throw new Exception($errorMessage, 2);
                                 }
                              }
                           }
                     }
                     if($response['status'] != "PAID"){
                           throw new Exception("Error on get transaction status. Response data not available.", 2);
                     }
                  } else {
                     $errorMessage = "Error on get transaction status. Airtel responded with Status Code ".$apiResponse['status']['code'].".";
                     if(\array_key_exists("response_code",$apiResponse['status'])){
                           switch ($apiResponse['status']['response_code']) {
                              case 'DP00800001000':
                                 $errorMessage = "Airtel response: Transaction ID not found.";
                                 break;
                              case 'DP00800001002':
                                 $errorMessage = "Airtel response: Incorrect Pin has been entered.";
                                 break;
                              case 'DP00800001003':
                                 $errorMessage = "Airtel response: Withdrawal amount limit exceeded.";
                                 break;
                              case 'DP00800001004':
                                 $errorMessage = "Airtel response: Invalid Amount.";
                                 break;
                              case 'DP00800001005':
                                 $errorMessage = "Airtel response: User didn't enter the pin.";
                                 break;
                              case 'DP00800001006':
                                 $errorMessage = "Airtel response: Transaction in pending state/pin not entered.";
                                 break;
                              case 'DP00800001007':
                                 $errorMessage = "Airtel response: Customer has insufficient funds to complete this transaction.";
                                 break;
                              case 'DP00800001008':
                                 $errorMessage = "Airtel response: The transaction was refused.";
                                 break;
                              case 'DP00800001009':
                                 $errorMessage = "Airtel response: The transaction was refused.";
                                 break;
                              case 'DP00800001010':
                                 $errorMessage = "Airtel response: Transaction not permitted to Payee.";
                                 break;
                              case 'DP00800001024':
                                 $errorMessage = "Error on get transaction status. The transaction was timed out.";
                                 break;
                              case 'DP00800001025':
                                 $errorMessage = "Airtel response: The transaction was not found.";
                                 break;
                           }
                     }
                     throw new Exception($errorMessage, 2);
                  }
               }else{
                  throw new Exception("Error on get transaction status. Response status details not available.", 2);
               }
         } else {
            if($apiResponse->status() == 404){
               throw new Exception("Airtel response: Transaction (not successfully submitted) ID not found.", 2);
            }else{
               throw new Exception("Error on get transaction status. Airtel responded with Status Code " .
                           $apiResponse->status().".", 2);
            }
         }
      } catch (\Throwable $e) {
         $response['status'] = "FAILED";
         if ($e->getCode() == 1 || $e->getCode() == 2) {
               $response['error'] = $e->getMessage();
         } else {
               $response['error'] = "Error on get transaction status. " . $e->getMessage();
         }
      }
      return (object)$response;

   }

   private function getToken($configs): array
   {

      $response = [
         "access_token" => '',
         "expires_in" => ''
      ];

      try {
         $fullURL = $configs['baseURL'] . "auth/oauth2/token";
         $apiResponse = Http::timeout($configs['timeout'])->withHeaders([
                  "Content-Type" => "application/json",
                  "Accept" => "*/*"
               ])->post($fullURL, [
                  "client_id" => $configs['clientId'],
                  "client_secret" =>  $configs['clientSecret'],
                  "grant_type" => $configs['grantType']
               ]);
         if ($apiResponse->status() >= 200 && $apiResponse->status() < 300) {
               $apiResponse = $apiResponse->json();
               $response['access_token'] = $apiResponse['access_token'];
               $response['expires_in'] = $apiResponse['expires_in'];
         } else {
               throw new Exception("Airtel API Get Token error. Airtel Money responded with status: " . $apiResponse->status().".",1);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
               throw new Exception($e->getMessage(), 1);
         }else{
               throw new Exception("Airtel API Get Token error. Details: " . $e->getMessage(), 1);
         }
      }
      return $response;
   }

   private function getTransactionId(object $dto): string
   {
      $transactionId = "D".\date('ymd')."T".
               \date('His')."A".$dto->accountNumber;
      if(\strlen($transactionId) > 25){
         $transactionId = "D".\date('ymd')."T".
                        \date('His')."A".\substr($dto->accountNumber,-10);
      }
      if(\strlen($transactionId ) < 25){
         $arrLetters=['A','B','C','D','E','F','G','H',
                  'I','J','K','L','M','N','O','P',
                  'Q','R','S','T','U','V','W','X',
                  'Y','Z'];
         $lenToAdd = 25-\strlen($transactionId );
         for ($i=0; $i < $lenToAdd; $i++) { 
            $transactionId .= $arrLetters[\rand(0,25)];
         }
      }
      return $transactionId;
   }

   private function getConfigs(string $urlPrefix):array
   {
      return [
            'tx_reference'=>\env(\strtoupper($urlPrefix).'_AIRTEL_TXREFERENCE'),
            'clientSecret'=>\env(\strtoupper($urlPrefix).'_AIRTEL_SECRET'),
            'clientId'=>\env(\strtoupper($urlPrefix).'_AIRTEL_ID'),
            'grantType'=>\env('AIRTEL_GRANT_TYPE'),
            'timeout'=>\env('AIRTEL_Http_Timeout'),
            'baseURL'=>\env('AIRTEL_BASE_URL')
         ];
   }
    
}
