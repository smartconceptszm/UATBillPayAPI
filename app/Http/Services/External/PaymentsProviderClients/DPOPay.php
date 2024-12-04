<?php

namespace App\Http\Services\External\PaymentsProviderClients;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Clients\ClientWalletCredentialsService;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Exception;

class DPOPay implements IPaymentsProviderClient
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private ClientWalletCredentialsService $clientWalletCredentialsService,
      private ClientWalletService $clientWalletService) 
   {}
    
   public function requestPayment(object $dto):object
   {

      $mainResponse=[
         'status' => 'SUBMISSION FAILED',
         'transactionId' => '',
         'error'=>'',
      ];

      try {
         $configs = $this->getConfigs($dto->wallet_id);
         $txTokenXML = $this->getTransactionTokenXML($configs,$dto);
         $configs['transactionToken'] = $this->getTransactionToken($configs, $txTokenXML);
         $chargeCardXML = $this->getChargeCardXML($configs, $dto);
         $fullURL = $configs['baseURL'];
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                 'Content-Type' => 'application/xml',
                                 'Accept' => 'application/xml',
                              ])->send('POST', $fullURL, [
                                    'body' => $chargeCardXML,
                              ]);
         
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
            $xmlObject = simplexml_load_string($apiResponse->body(), "SimpleXMLElement", LIBXML_NOCDATA);
            $jsonArray = json_decode(json_encode($xmlObject), true);
            if($jsonArray['Result'] == '000'){
               $mainResponse['transactionId'] = $configs['transactionToken'];
               $mainResponse['status'] = 'SUBMITTED';
            }else{
               throw new Exception("DPOPay Charge Card error: ".$jsonArray['ResultExplanation'].".", 1);
            }
         } else{
            throw new Exception("DPOPay Charge Card error. DPOPay responded with status code: ".$apiResponse->status().".", 1);
         }
      } catch (\Throwable $e) {
         if($e->getCode()==1){
               $mainResponse['error']=$e->getMessage();
         }else{
               $mainResponse['error']="DPOPay Charge Card error. DPOPay details: ".$e->getMessage();
         }
      }
      return (object)$mainResponse;
      
   }

   public function confirmPayment(object $dto): object
   {

      $response=['status'=>"PAYMENT FAILED",
                  'ppTransactionId'=>'',
                  'error'=>''];

      try {
         $configs = $this->getConfigs($dto->wallet_id);
         $theXML = '<?xml version="1.0" encoding="utf-8"?>
                     <API3G>
                        <CompanyToken>'.$configs['companyToken'].'</CompanyToken>
                        <Request>verifyToken</Request>
                        <TransactionToken>'.$dto->transactionId.'</TransactionToken>
                     </API3G>';

         $fullURL = $configs['baseURL'];
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Content-Type' => 'application/xml',
                                    'Accept' => 'application/xml',
                                 ])
                              ->send('POST', $fullURL, [
                                    'body' => $theXML,
                                 ]);
         
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
            $xmlObject = simplexml_load_string($apiResponse->body(), "SimpleXMLElement", LIBXML_NOCDATA);
            $jsonArray = json_decode(json_encode($xmlObject), true);
            if($jsonArray['Result'] == '000'){
               $response['ppTransactionId'] = $jsonArray['TransactionApproval'];
               $response['status'] = 'PAYMENT SUCCESSFUL';
            }else{
               throw new Exception("DPOPay verify token error: ".$jsonArray['ResultExplanation'].".", 1);
            }
         } else{
            throw new Exception("DPOPay verify token error. DPOPay responded with status code: ".$apiResponse->status().".", 1);
         }
      } catch (\Throwable $e) {
         if ($e->getCode()==1) {
            $response['error']=$e->getMessage();
         } else {
            $response['error']="DPOPay verify token error. ".$e->getMessage();
         }
      }
      return (object)$response;

   }

   private function getTransactionToken(array $configs, string $theXML): string
   {

      $response='';

      try {
         $fullURL = $configs['baseURL'];
         $apiResponse = Http::timeout($configs['timeout'])
               ->withHeaders([
                     'Content-Type' => 'application/xml',
                     'Accept' => 'application/xml',
                  ])
               ->send('POST', $fullURL, [
                     'body' => $theXML,
                  ]);
         
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
            $xmlObject = simplexml_load_string($apiResponse->body(), "SimpleXMLElement", LIBXML_NOCDATA);
            $jsonArray = json_decode(json_encode($xmlObject), true);
            if($jsonArray['Result'] == '000'){
               $response=$jsonArray['TransToken'];
            }else{
               throw new Exception("DPOPay API Get Transaction Token error: ".$jsonArray['ResultExplanation'].".", 1);
            }
         } else{
               throw new Exception("DPOPay API Get Transaction Token error. DPOPay responded with status code: ".$apiResponse->status().".", 1);
         }

      } catch (\Throwable $e) {
         if ($e->getCode()==1) {
               throw new Exception($e->getMessage(), 1);
         } else {
               throw new Exception("DPOPay API Get Transaction Token error. Details: ".$e->getMessage(), 1);
         }
      }
      return $response;
   }

   private function getTransactionTokenXML(array $configs, object $params):string
   {
      $transactionDate = Carbon::now();
      $transactionDate = $transactionDate->format('Y/m/d H:i');
      $xmlTemplate = '<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G>';
      $xmlTemplate .= '<CompanyToken>'.$configs['companyToken'].'</CompanyToken>
                     <Request>createToken</Request>
                     <Transaction>
                        <PaymentAmount>'.$params->paymentAmount.'</PaymentAmount>
                        <PaymentCurrency>'.$configs['currency'].'</PaymentCurrency>
                        <CompanyRef>'.$configs['companyRef'].'</CompanyRef>
                        <RedirectURL>http://www.domain.com/payurl.php</RedirectURL>
                        <BackURL>http://www.domain.com/backurl.php </BackURL>
                        <CompanyRefUnique>0</CompanyRefUnique>
                        <PTL>5</PTL>
                     </Transaction>
                     <Services>
                        <Service>
                              <ServiceType>'.$configs['serviceType'].'</ServiceType>
                              <ServiceDescription>'.$configs['serviceDescription'].'</ServiceDescription>
                              <ServiceDate>'.$transactionDate.'</ServiceDate>
                        </Service>
                     </Services>
                  </API3G>';
      // $xmlTemplate = new SimpleXMLElement($xmlTemplate);
      return $xmlTemplate;

   }

   private function getChargeCardXML(array $configs,  object $dto):string
   {

      $cardExpiry = Carbon::createFromFormat('Y-m-d',$dto->cardExpiry.'-01');
      $cardExpiry = $cardExpiry->format('my');

      return '<?xml version="1.0" encoding="utf-8"?>
                                    <API3G>
                                       <CompanyToken>'.$configs['companyToken'].'</CompanyToken>
                                       <Request>chargeTokenCreditCard</Request>
                                       <TransactionToken>'.$configs['transactionToken'].'</TransactionToken>
                                       <CreditCardNumber>'.$dto->creditCardNumber.'</CreditCardNumber>
                                       <CreditCardExpiry>'.$cardExpiry.'</CreditCardExpiry>
                                       <CreditCardCVV>'.$dto->cardCVV.'</CreditCardCVV>
                                       <CardHolderName>'.$dto->cardHolderName.'</CardHolderName>
                                       <ChargeType></ChargeType>
                                    </API3G>';
   }

   private function getConfigs(string $wallet_id):array
   {

      $walletCredentials = $this->clientWalletCredentialsService->getWalletCredentials($wallet_id);   
      $clientWallet = $this->clientWalletService->findById($wallet_id);
      $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);

      return [
            'serviceDescription'=>$walletCredentials['DPO_ServiceDescription'],
            'companyToken'=>$walletCredentials['DPO_CompanyToken'],
            'serviceType'=>$walletCredentials['DPO_ServiceType'],
            'companyRef'=>$walletCredentials['DPO_CompanyRef'],
            'currency'=>$walletCredentials['DPO_Currency'],
            'timeout'=>$paymentsProviderCredentials['DPOPay_Http_Timeout'],
            'baseURL'=>$paymentsProviderCredentials['DPOPay_BASE_URL']
         ];

   }

}