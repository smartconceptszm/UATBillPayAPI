<?php

namespace App\Listeners;

use App\Http\BillPay\Services\Utility\PsrMessageToStringConvertor;
use Illuminate\Http\Client\Events\ResponseReceived;
use App\Jobs\SendSMSNotificationsJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class HttpCallErrorsLogger
{

   private $messageConvertor;

   /**
    * Create the event listener.
    *
    * @return void
    */
   public function __construct(PsrMessageToStringConvertor $messageConvertor)
   {
      $this->messageConvertor= $messageConvertor;
   }

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

         $arrConfig=\config('efectivo_clients');
         foreach ($arrConfig as $key => $value) {
               if(substr($event->request->url(),0,strlen(\env(\strtoupper($key).'_base_URL')))==\env(\strtoupper($key).'_base_URL')){
                  $logString.=\env(\strtoupper($key).'_base_URL');
                  $errorType=\env(\strtoupper($key).'_base_URL');
               }
         }

         foreach ($mnos as $mno) {
               if(\strpos($event->request->url(),$mno)){
                  $logString.=\strtoupper($mno)." MONEY";
                  $errorType=\strtoupper($mno)."MONEY";
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
               if (($httpErrorCount+1) < (int)\env('Http_Error_THRESHOLD')) {
                  Cache::increment($errorType.'_ErrorCount');
               }else{
                  $httpErrorCount = 1;
                  //Send Notification here
                     $adminMobileNumbers = \explode("*",\env('APP_ADMIN_MSISDN'));
                     $arrSMSes=[];
                     foreach ($adminMobileNumbers as $key => $mobileNumber) {
                           $arrSMSes[$key]['clientShortName']="SCL";
                           $arrSMSes[$key]['mobileNumber']=$mobileNumber;
                           $arrSMSes[$key]['message']="Http calls to ".$errorType
                                                      ." have failed over 5 times in the last ".
                                                      env('Http_Error_CACHE')." minutes!";
                     }
                     Queue::later(Carbon::now()->addSeconds(1), new SendSMSNotificationsJob($arrSMSes));
                  //
                  Cache::put($errorType.'_ErrorCount', $httpErrorCount, Carbon::now()->addMinutes((int)env('Http_Error_CACHE')));
               } 
         }else{
               Cache::put($errorType.'_ErrorCount', 1, Carbon::now()->addMinutes((int)env('Http_Error_CACHE')));
         }
      }else{

         $logString="";
         foreach ($mnos as $mno) {
               if(\strpos($event->request->url(),$mno) && \env(\strtoupper($mno).'_LOG_ALL')=='YES'){
                  $logString=\strtoupper($mno)." MONEY";
                  $errorType=\strtoupper($mno)."MONEY";
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

