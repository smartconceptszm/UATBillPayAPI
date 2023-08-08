<?php

namespace App\Http\BillPay\Services\External\MoMoClients;

use App\Http\BillPay\Services\External\MoMoClients\IMoMoClient;
use Illuminate\Support\Facades\Http;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class ZamtelKwacha implements IMoMoClient
{

   public function requestPayment(object $dto): object
   {
      return (object)[
            'transactionId' => substr($dto->mobileNumber,2,10).date('YmdHis'),
            'status' => 'SUBMITTED',
            'error' => '',
         ];
   }
   
   public function confirmPayment(object $dto): object
   {

      $mainResponse=[
            'status'=>"FAILED",
            'mnoTransactionId'=>'',
            'error'=>''
         ];

      try {
         $configs = $this->getConfigs($dto->urlPrefix);
         //ThirdPartyID=&Password=&Amount=1&Msisdn=260956099652&Shortcode=000088&ConversationId=00001
         $zamtelURI="ThirdPartyID=".trim($configs['clientId']);
         $zamtelURI.="&Password=".trim($configs['clientSecret']);
         $zamtelURI.="&Amount=".$dto->paymentAmount;
         $zamtelURI.="&Msisdn=".trim($dto->mobileNumber);
         $zamtelURI.="&Shortcode=".trim($configs['shortCode']);
         $zamtelURI.="&ConversationId=".trim($dto->transactionId);
         $fullURL = $configs['baseURL'].$zamtelURI;
         $apiResponse = Http::timeout($configs['timeout'])->withHeaders([
            'Content-Type' => 'application/json',
            "Accept" => "*/*",
         ])->get($fullURL);
         //{"Conversation id":"00001","message":"Success","status":"0","TransactionId":"000000239171","Transaction id":"000000239171"}
         if($apiResponse->status()>=200 && $apiResponse->status()<300 ){
            $apiResponse=$apiResponse->json();
            if($apiResponse['status']==='0'){
               $mainResponse['status'] = "PAID";
               $mainResponse['mnoTransactionId']=$apiResponse['TransactionId'];
            }else{
               $errorMessage='';
               switch ($apiResponse['status']) {
                     case 'E8003':
                        $errorMessage='Wrong PIN.';
                        break;
                     case '2006':
                        $errorMessage='Balance insufficient.';
                        break;
                     case '2004':
                        $errorMessage='Amount invalid.';
                        break;
                     case 'E8036':
                        $errorMessage='Customer not registered';
                        break;
                     default:
                        $errorMessage='System Time Out';
                        break;
               }
               throw new Exception($errorMessage,1);
            }
         }else{
            throw new Exception($apiResponse->status(), 2);
         }
      } catch (\Throwable $e){
         if($e->getCode()==1){
               $mainResponse['error']=$e->getMessage();
         }else{
               $mainResponse['error']="Zamtel Kwacha unavailable";
         }
      }
      return (object)$mainResponse;

   }

   private function getConfigs(string $urlPrefix):array
   {
      return [
            'clientId'=>\env(\strtoupper($urlPrefix).'_ZAMTEL_THIRDPARTYID'),
            'clientSecret'=>\env(\strtoupper($urlPrefix).'_ZAMTEL_PASSWORD'),
            'shortCode'=>\env(\strtoupper($urlPrefix).'_ZAMTEL_SHORTCODE'),
            'timeout'=>\env('ZAMTEL_Http_Timeout'),
            'baseURL'=>\env('ZAMTEL_BASE_URL')
         ];
   }
    
}