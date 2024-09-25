<?php

namespace App\Http\Services\External\PaymentsProviderClients;

use App\Http\Services\External\PaymentsProviderClients\IPaymentsProviderClient;
use App\Http\Services\Web\Clients\ClientWalletCredentialsService;
use App\Http\Services\Web\Clients\ClientWalletService;
use Illuminate\Support\Facades\Http;
use Exception;

class ZamtelKwacha implements IPaymentsProviderClient
{

   public function __construct(
      private ClientWalletCredentialsService $clientWalletCredentialsService,
      private ClientWalletService $clientWalletService) 
   {}

   public function requestPayment(object $dto): object
   {

      return (object)[
                     'transactionId' => substr($dto->walletNumber,2,10).date('YmdHis'),
                     'status' => 'SUBMITTED',
                     'error' => '',
                  ];

   }
   
   public function confirmPayment(object $dto): object
   {

      try {

         $dto->transactionId = substr($dto->walletNumber,2,10).date('YmdHis');
         $mainResponse = [
               'transactionId' =>$dto->transactionId,
               'status'=>"PAYMENT FAILED",
               'ppTransactionId'=>'',
               'error'=>''
            ];

         $configs = $this->getConfigs($dto->wallet_id);
         $theXML = $this->getPaymentXML($configs,$dto);
         $fullURL = $configs['baseURL'];
         $apiResponse = Http::timeout($configs['timeout'])
                              ->withHeaders([
                                    'Content-Type' => 'text/xml',
                                    'Accept' => '*/*',
                                 ])
                              ->send('POST', $fullURL, [
                                    'body' => $theXML,
                                 ]);
         
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
            $xmlBody = $apiResponse->body();
            $xmlObject = simplexml_load_string($xmlBody, 'SimpleXMLElement', LIBXML_NOCDATA);
            $namespaces = $xmlObject->getNamespaces(true);
            $parsedXMLBody = $xmlObject->children($namespaces['soapenv'])->Body->children($namespaces['api'])->Result->children($namespaces['res'])->Body;
            $jsonArray = json_decode(json_encode($parsedXMLBody), true);

            if($jsonArray['ResultCode'] == '0'){
               $mainResponse['ppTransactionId'] = $jsonArray['TransactionResult']['TransactionID'];
               $mainResponse['status'] = 'PAID | NOT RECEIPTED';
            }else{
               $errorMessage='';
               switch ($jsonArray['ResultCode']) {
                  case 'E8027':
                     $errorMessage='System not available';
                     break;
                  case '2001':
                        $errorMessage='Initiator authentication error';
                        break;
                  case '-1':
                        $errorMessage='System internal error';
                        break;
                  default:
                     $errorMessage='System internal error';
                  break;
               }
               throw new Exception("Zamtel MaKwacha response: ".$errorMessage,1);
            }
         } else{
            throw new Exception("Zamtel MaKwacha responded with status code: ".$apiResponse->status().".", 2);
         }
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $mainResponse['error']=$e->getMessage();
         }else{
            $mainResponse['error']="Zamtel Kwacha unavailable";
         }
      }

      return (object)$mainResponse;

   }


   public function comfirmZamtel(object $dto) : object {
      try {

         $response = ['status'=>"PAYMENT FAILED",
                        'ppTransactionId'=>'',
                        'error'=>''];

         $configs = $this->getConfigs($dto->wallet_id);

         $fullURL = $configs['statusBaseURL'].$dto->transactionId;
         $apiResponse = Http::timeout($configs['timeout'])->withHeaders([
                                          'Content-Type' => 'application/json',
                                          'username' => 'mobilemoney',
                                          'password' => 'test',
                                          'Accept' => '*/*'   
                                       ])
                                       ->get($fullURL);
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
            $apiResponse=$apiResponse->json();
            if($apiResponse['code']==='200'){
               $response['status'] = "PAID | NOT RECEIPTED";
               $response['ppTransactionId']=$apiResponse['ORDERID'];
            }else{
               throw new Exception("Error on get transaction status. ZamKwacha response: ". $apiResponse['ORDERSTATE'].".", 2);
            }
         }else{
            throw new Exception("Error on get transaction status. ZamKwacha response: Status Code ".$apiResponse->status().".", 2);
         }
      } catch (\Throwable $e) {
         if ($e->getCode()==2 || $e->getCode()==3) {
            $response['error']=$e->getMessage();
         } else {
            $response['error']="Error on get transaction status. ".$e->getMessage();
         }
      }
      return (object)$response;
   }

   private function getPaymentXML(array $configs,  object $dto):string
   {

      return   '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
                  xmlns:api="http://cps.huawei.com/synccpsinterface/api_requestmgr"
                  xmlns:com="http://cps.huawei.com/synccpsinterface/common"
                  xmlns:cus="http://cps.huawei.com/cpsinterface/customizedrequest"
                  xmlns:req="http://cps.huawei.com/synccpsinterface/request">
                  <soapenv:Body>
                     <api:Request>
                        <req:Header>
                              <req:Version>1.0</req:Version>
                              <req:CommandID>InitTrans_Customer Pay Organization Bill</req:CommandID>
                              <req:OriginatorConversationID>'.$dto->transactionId.'</req:OriginatorConversationID>
                              <req:Caller>
                                 <req:CallerType>2</req:CallerType>
                                 <req:ThirdPartyID>'.$configs['THIRDPARTYID'].'</req:ThirdPartyID>
                                 <req:Password>'.$configs['PASSWORD'].'</req:Password>
                              </req:Caller>
                              <req:KeyOwner>1</req:KeyOwner>
                              <req:Timestamp>'.date('YmdHis').'</req:Timestamp>
                        </req:Header>
                        <req:Body>
                              <req:Identity>
                                 <req:Initiator>
                                    <req:IdentifierType>1</req:IdentifierType>
                                    <req:Identifier>'.$dto->walletNumber.'</req:Identifier>
                                 </req:Initiator>
                                 <req:ReceiverParty>
                                    <req:IdentifierType>4</req:IdentifierType>
                                    <req:Identifier>'.$configs['SHORTCODE'].'</req:Identifier>
                                 </req:ReceiverParty>
                              </req:Identity>
                              <req:TransactionRequest>
                                 <req:Parameters>
                                    <req:Parameter>
                                          <com:Key>BillReferenceNumber</com:Key>
                                          <com:Value>82eb11df-49ac-46cf-b608-9ed448beb165</com:Value>
                                    </req:Parameter>
                                    <req:Amount>'.$dto->paymentAmount.'</req:Amount>
                                    <req:Currency>ZMW</req:Currency>
                                 </req:Parameters>
                              </req:TransactionRequest>
                        </req:Body>
                     </api:Request>
                  </soapenv:Body>
               </soapenv:Envelope>';
   }

   private function getConfigs(string $wallet_id):array
   {
      $walletCredentials = $this->clientWalletCredentialsService->getWalletCredentials($wallet_id);
      //$theWallet = $this->clientWalletService->findById($wallet_id);
   
      return [
            'THIRDPARTYID'=>$walletCredentials['ZAMTEL_THIRDPARTYID'],
            'PASSWORD'=>$walletCredentials['ZAMTEL_PASSWORD'],
            'SHORTCODE'=>$walletCredentials['ZAMTEL_SHORTCODE'],
            'timeout'=>\env('ZAMTEL_Http_Timeout'),
            'baseURL'=>\env('ZAMTEL_BASE_URL'),
            'statusBaseURL'=>\env('ZAMTEL_STATUS_BASE_URL')
         ];
   }
    
}