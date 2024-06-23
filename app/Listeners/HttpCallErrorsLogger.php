<?php

namespace App\Listeners;

use App\Http\Services\Web\Clients\BillingCredentialService;
use App\Http\Services\Utility\PsrMessageToStringConvertor;
use Illuminate\Http\Client\Events\ResponseReceived;
use App\Http\Services\Web\Clients\ClientService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
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

      $mnos=['airtel','mtn','zamtel'];
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

         foreach ($mnos as $mno) {
            if(\strpos($event->request->url(),$mno)){
               $logString.=\strtoupper($mno)." MONEY";
               $errorType=\strtoupper($mno)."MONEY";
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
            if (($httpErrorCount+1) < (int)\env('HTTP_ERROR_THRESHOLD')) {
               Cache::increment($errorType.'_ErrorCount');
            }else{
               $httpErrorCount = 1;
               //Send Notification here
                  $adminMobileNumbers = \explode("*",\env('APP_ADMIN_MSISDN'));
                  $arrSMSes=[];
                  foreach ($adminMobileNumbers as $key => $mobileNumber) {
                        $arrSMSes[$key]['urlPrefix']="scl";
                        $arrSMSes[$key]['shortName']="SCL";
                        $arrSMSes[$key]['type']="NOTIFICATION";
                        $arrSMSes[$key]['mobileNumber']=$mobileNumber;
                        $arrSMSes[$key]['message']="Http calls to ".$errorType
                                                   ." have failed over 5 times in the last ".
                                                   env('HTTP_ERROR_CACHE')." minutes!";
                  }
                  Queue::later(Carbon::now()->addSeconds(1), new SendSMSesJob($arrSMSes),'','low');
               //
               Cache::put($errorType.'_ErrorCount', $httpErrorCount, Carbon::now()->addMinutes((int)env('HTTP_ERROR_CACHE')));
            } 
         }else{
            Cache::put($errorType.'_ErrorCount', 1, Carbon::now()->addMinutes((int)env('HTTP_ERROR_CACHE')));
         }
      }else{

         $logString="";
         foreach ($mnos as $mno) {
            if(\strpos($event->request->url(),$mno) && \env(\strtoupper($mno).'_LOG_ALL')=='YES'){
               $logString=\strtoupper($mno)." MONEY";
               $errorType=\strtoupper($mno)."MONEY";
               break;
            }
         }

         if($logString){
            $logString="\n\n*******************************\n\n".$logString;
            $logString.="Http Request and Response log:\n\n";
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

