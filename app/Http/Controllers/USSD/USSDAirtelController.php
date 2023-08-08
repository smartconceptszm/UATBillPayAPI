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
            $airtelParams['subscriberInput']=\strtoupper($request->input('SUBSCRIBER_INPUT'));
            $airtelParams['isNewRequest']=$request->input('IS_NEW_REQUEST');
            $airtelParams['sessionId']=$request->input('SESSION_ID');
            $airtelParams['mobileNumber']=$request->input('MSISDN');
            $airtelParams['urlPrefix']=$this->getUrlPrefix($request);
            $airtelParams['mnoName']='AIRTEL';
            $this->theDTO=$this->theDTO->fromArray($airtelParams);
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
