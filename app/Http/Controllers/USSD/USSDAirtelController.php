<?php

namespace App\Http\Controllers\USSD;

use App\Http\Controllers\USSD\USSDController;
use Illuminate\Http\Request;

class USSDAirtelController extends USSDController
{

   public function index(Request $request)
   {

      try {
         //Parse RequestParameters
            //"GET /lukanga/airtel?MSISDN=260977787659&SUBSCRIBER_INPUT=2106&SESSION_ID=16745872760811617&IS_NEW_REQUEST=1 HTTP/1.1" 200 5 "-" "Java/1.8.0_211"
            
            //"GET /swasco/airtel?SUBSCRIBER_INPUT=5757&LANGUAGE=en&SESSION_ID=16318751934311429&MOBILE_NUMBER=260972116857&
            //IS_NEW_REQUEST=1&USSD_MESSAGE=&SEQUENCE=&END_OF_SESSION=&FRA=&SERVICE_KEY
            
            $requestParams = $this->getParameters($request);
            $airtelParams['subscriberInput']=\strtoupper($requestParams['SUBSCRIBER_INPUT']);
            $airtelParams['isNewRequest']=$requestParams['IS_NEW_REQUEST'];
            $airtelParams['sessionId']=$requestParams['SESSION_ID'];
            if(\array_key_exists('MSISDN',$requestParams)){
               $airtelParams['mobileNumber']=$requestParams['MSISDN'];
            }else{
               $airtelParams['mobileNumber']=$requestParams['MOBILE_NUMBER'];
            }
            $airtelParams['urlPrefix']=$this->getUrlPrefix($request);
            $airtelParams['mnoName'] = 'AIRTEL';
            $mno = $this->getMno($airtelParams['mnoName']);
            $airtelParams['payments_provider_id'] = $mno->payments_provider_id;
            $airtelParams['mno_id'] = $mno->id; 
            $this->ussdDTO = $this->ussdDTO->fromArray($airtelParams);
         //Process the Request
         $this->ussdDTO = $this->ussdService->handle($this->ussdDTO);

      } catch (\Throwable $e) {
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         $this->ussdDTO->error = 'Error: At Airtel controller level. '.$e->getMessage();
         $this->ussdDTO->response =$billpaySettings['ERROR_MESSAGE'];
         $this->ussdDTO->lastResponse = true;
      }
      return $this->responder($request);

   }
    
}
