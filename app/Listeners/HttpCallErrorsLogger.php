<?php

namespace App\Listeners;

use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Utility\PsrMessageToStringConvertor;
use Illuminate\Http\Client\Events\ResponseReceived;
use App\Http\Services\Clients\ClientService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;

class HttpCallErrorsLogger
{

   /**
    * Create the event listener.
    *
    * @return void
    */
   public function __construct(
      private BillingCredentialService $billingCredentialService,
      private PsrMessageToStringConvertor $messageConvertor, 
      private ClientService $clientService)
   {}

   /**
    * Handle the event.
    *
    * @param  \App\Events\ExampleEvent  $event
    * @return void
    */
   public function handle(ResponseReceived $event)
   {

      $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
      $paymentsproviders=['airtel','mtn','zamtel','3gdirectpay'];
      if($event->response->status()<200 || $event->response->status()>299){

         $errorType='';
         $logString="\n\n*******************************\n\n";

         $clients = $this->clientService->findAll(['status' => 'ACTIVE']);
         foreach ( $clients as $client) {
            $billingCredentials = $this->billingCredentialService->findAll(['client_id'=>$client->id]);
            foreach ($billingCredentials as $key => $value) {
               if(\substr($key, -7) == 'BASE_URL'){
                  if(\strpos($event->request->url(),$value)){
                     $logString.=$value;
                     $errorType=$value;
                     break;
                  }
               }
            }
         }

         foreach ($paymentsproviders as $paymentsprovider) {
            if(\strpos($event->request->url(),$paymentsprovider)){
               if(\strpos($event->request->url(),'sms')){
                  $logString.=\strtoupper($paymentsprovider)." SMS";
                  $errorType=\strtoupper($paymentsprovider)."SMS";
               }else{
                  $logString.=\strtoupper($paymentsprovider)." MONEY";
                  $errorType=\strtoupper($paymentsprovider)."MONEY";
               }
               break;
            }
         }

         if(\strpos($event->request->url(),"bsms")){
               $logString.="SMS GATEWAY";
               $errorType="SMS_GATEWAY";
         }
         
         $logString.=" Http REQUEST log:\n\n";
         $logString.="The REQUEST";
         $logString.=$this->messageConvertor->toString($event->request->toPsrRequest());
         $logString.="\nThe RESPONSE";
         $logString.="\n".$this->messageConvertor->toString($event->response->toPsrResponse());
         $logString.="\n\n*******************************\n";
         Log::error($logString);

         
         $httpErrorCount = (int)Cache::get($errorType.'_ErrorCount');
         if($httpErrorCount){       
            if (($httpErrorCount+1) < (int)$billpaySettings['HTTP_ERROR_THRESHOLD']) {
               Cache::increment($errorType.'_ErrorCount');
            }else{
               $httpErrorCount = 1;
               //Send Notification here
                  $adminMobileNumbers = \explode("*",$billpaySettings['APP_ADMIN_MSISDN']);
                  $arrSMSes=[];
                  foreach ($adminMobileNumbers as $key => $mobileNumber) {
                        $arrSMSes[$key]['shortName']="SCL";
                        $arrSMSes[$key]['type']="NOTIFICATION";
                        $arrSMSes[$key]['mobileNumber']=$mobileNumber;
                        $arrSMSes[$key]['message']="Http calls to ".$errorType
                                                   ." have failed over 5 times in the last ".
                                                   $billpaySettings['HTTP_ERROR_CACHE']." minutes!";
                  }
                  SendSMSesJob::dispatch($arrSMSes)
                                 ->delay(Carbon::now()->addSeconds(1))
                                 ->onQueue('low');
               //
               Cache::put($errorType.'_ErrorCount', $httpErrorCount, Carbon::now()->addMinutes((int)$billpaySettings['HTTP_ERROR_CACHE']));
            } 
         }else{
            Cache::put($errorType.'_ErrorCount', 1, Carbon::now()->addMinutes((int)$billpaySettings['HTTP_ERROR_CACHE']));
         }
      }else{

         $logString="";

         foreach ($paymentsproviders as $paymentsprovider) {
            if(\strpos($event->request->url(),$paymentsprovider) && $billpaySettings['HTTP_CALLS_LOG_ALL']=='YES'){
               $logString=\strtoupper($paymentsprovider)." MONEY";
               $errorType=\strtoupper($paymentsprovider)."MONEY";
               break;
            }
         }

         if($logString){
            $logString="\n\n*******************************\n\n".$logString;
            $logString.=" Http Request and Response log:\n\n";
            $logString.="The REQUEST";
            $logString.=$this->messageConvertor->toString($event->request->toPsrRequest());
            $logString.="\nThe RESPONSE";
            $logString.="\n".$this->messageConvertor->toString($event->response->toPsrResponse());
            $logString.="\n\n*******************************\n";
            Log::info($logString);
         }
      }

   }
    
}

