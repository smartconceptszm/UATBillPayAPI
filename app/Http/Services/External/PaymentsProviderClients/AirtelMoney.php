<?php

namespace App\Http\Services\External\PaymentsProviderClients;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Clients\ClientWalletCredentialsService;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Http;
use Exception;

class AirtelMoney implements IPaymentsProviderClient
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private ClientWalletCredentialsService $clientWalletCredentialsService,
      private ClientWalletService $clientWalletService) 
   {}

   public function requestPayment(object $dto):object
   {

      $mainResponse = [
            'status' => 'SUBMISSION FAILED',
            'transactionId' => '',
            'error' => ''
         ];

      try {
         $mainResponse['transactionId'] = $this->getTransactionId($dto->customerAccount);
         $configs = $this->getConfigs($dto->wallet_id);
         $apiToken = $this->getToken($configs);
         $token = $apiToken['access_token'];

         $fullURL = $configs['baseURL'] . "merchant/v1/payments/";
         $airtelResponse = Http::timeout($configs['timeout'])
                                 ->withHeaders([
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
                                          "msisdn" => \substr($dto->walletNumber,3,9),
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
                                    $mainResponse['status'] = "SUBMITTED";
                              }else{
                                    if (\array_key_exists('message', $airtelResponse['data']['transaction'])) {
                                       $errorMessage = "Error on collect funds. Airtel response: ".$airtelResponse['data']['transaction']['message'].".";
                                    }else{
                                       $errorMessage = "Error on collect funds. ".$airtelResponse['data']['transaction']['status'].".";
                                    }
                                    throw new Exception($errorMessage, 2);
                              }
                           }
                        }
                  }
                  if($mainResponse['status'] != "SUBMITTED"){
                     throw new Exception("Error on collect funds. Response data not available.", 2);
                  }
               }else{
                  $message = "Airtel responded with Status Code ".$airtelResponse['status']['code'].".";
                  if(\array_key_exists('response_code', $airtelResponse['status'])){
                     $message = $this->getErrorMessage($airtelResponse['status']['response_code']);
                  }else{
                     if (\array_key_exists('message', $airtelResponse['status'])){
                        $message = "Airtel response: ".$airtelResponse['status']['message'];
                     }
                  }
                  $errorMessage = "Error on collect funds. ".$message;
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
      
      
      return (object)$mainResponse;
   }

   public function confirmPayment(object $dto):object
   {

      $response = [
         "status" => "PAYMENT FAILED",
         "ppTransactionId" => "",
         "error" => ""
      ];

      try {
         $configs = $this->getConfigs($dto->wallet_id);
         $apiToken = $this->getToken($configs);
         $token = $apiToken['access_token'];
         $fullURL = $configs['baseURL'] . "standard/v1/payments/".$dto->transactionId;
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    "Content-Type" => "application/json",
                                    "Accept" => "*/*",
                                    "X-Country" => "ZM",
                                    "X-Currency" => "ZMW",
                                    "Authorization" => "Bearer ".$token
                                 ])
                              ->get($fullURL);
         if ($apiResponse->status() >= 200 && $apiResponse->status() < 300) {
               $apiResponse = $apiResponse->json();
               if (\array_key_exists('status', $apiResponse)) {
                  if ($apiResponse['status']['code'] === "200"){
                     if (\array_key_exists('data', $apiResponse)) {
                           if (\array_key_exists('transaction', $apiResponse['data'])) {
                              if (\array_key_exists('status', $apiResponse['data']['transaction'])) {
                                 if($apiResponse['data']['transaction']['status'] == "TS"){
                                       $response['status'] = "PAYMENT SUCCESSFUL";
                                       $response['ppTransactionId'] = $apiResponse['data']['transaction']['airtel_money_id'];
                                 }else{
                                    $errorMessage = "Airtel response: ".$apiResponse["data"]["transaction"]["status"].".";
                                    throw new Exception($errorMessage, 2);
                                 }
                              }
                           }
                     }
                     if($response['status'] != "PAYMENT SUCCESSFUL"){
                        throw new Exception("Error on get transaction status. Response data not available.", 2);
                     }
                  } else {
                     $errorMessage = "Error on get transaction status. Airtel responded with Status Code ".$apiResponse['status']['code'].".";
                     if(\array_key_exists("response_code",$apiResponse['status'])){
                        $errorMessage = "Error on get transaction status. ".$this->getErrorMessage($apiResponse['status']['response_code']);
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
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    "Content-Type" => "application/json",
                                    "Accept" => "*/*"
                                 ])
                              ->post($fullURL, [
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

   private function getTransactionId(string $customerAccount): string
   {

      $theDate = "D".\date('ymd');
      $theTime = "T".\date('His');
      $theAccount = "A".$customerAccount;
      $transactionId = $theAccount.$theDate.$theTime;
      if(\strlen($transactionId) > 25){
         $theAccount = \substr($theAccount,0,25-strlen($theDate.$theTime));
         $transactionId = $theAccount.$theDate.$theTime;
      }
      if(\strlen($transactionId ) < 25){
         $arrLetters=['A','B','C','D','E','F','G','H',
                  'I','J','K','L','M','N','O','P',
                  'Q','R','S','T','U','V','W','X',
                  'Y','Z'];
         $lenToAdd = 25-\strlen($transactionId );
         $strToApend = '';
         for ($i=0; $i < $lenToAdd; $i++) { 
            $strToApend .= $arrLetters[\rand(0,25)];
         }
         $transactionId = $theAccount.$theDate.$theTime;
      }
      return $transactionId;
   }

   public function getErrorMessage(String $errorCode): string
   {
      $errorMessage = "Not reachable";
      switch ($errorCode) {
         case 'DP00800001000':
            $errorMessage = "Airtel response: The transaction is still processing and is in ambiguous state.";
            break;
         case 'DP00800001002':
            $errorMessage = "Airtel response: Incorrect Pin has been entered.";
            break;
         case 'DP00800001003':
            $errorMessage = "Airtel response: Withdrawal amount limit exceeded.";
            break;
         case 'DP00800001004':
            $errorMessage = "Airtel response: The amount User is trying to transfer is less than the minimum amount allowed.";
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
         case 'DP00800001026':
            $errorMessage = "Airtel response: X-signature and payload did not match";
            break;
         case 'DP00800001026':
            $errorMessage = "Airtel response: Transaction has been expired";
            break;
      }
      return $errorMessage;
   }

   private function getConfigs(string $wallet_id):array
   {

      $walletCredentials = $this->clientWalletCredentialsService->getWalletCredentials($wallet_id);
      $clientWallet = $this->clientWalletService->findById($wallet_id);
      $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);
      
      return [
            'tx_reference'=>$walletCredentials['AIRTEL_TXREFERENCE'],
            'clientSecret'=>$walletCredentials['AIRTEL_SECRET'],
            'clientId'=>$walletCredentials['AIRTEL_ID'],
            'grantType'=>$paymentsProviderCredentials['AIRTEL_GRANT_TYPE'],
            'timeout'=>$paymentsProviderCredentials['AIRTEL_Http_Timeout'],
            'baseURL'=>$paymentsProviderCredentials['AIRTEL_BASE_URL']
         ];
   }

    
}
