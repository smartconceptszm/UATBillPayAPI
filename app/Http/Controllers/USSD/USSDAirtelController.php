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
            
            $requestParams = $request->all();
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
            $airtelParams['mno_id'] = $this->getMnoId($airtelParams['mnoName']); 
            $this->theDTO = $this->theDTO->fromArray($airtelParams);
         //Process the Request
         $this->theDTO = $this->theService->handle($this->theDTO);

      } catch (\Throwable $e) {
         $this->theDTO->error = 'Error: At Airtel controller level. '.$e->getMessage();
         $this->theDTO->response = \env('ERROR_MESSAGE');
         $this->theDTO->lastResponse = true;
      }
      return $this->responder($request);

   }
    
}
