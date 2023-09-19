<?php

namespace App\Http\Controllers\USSD;

use App\Http\Controllers\USSD\USSDController;
use Illuminate\Http\Request;

class USSDMTNController extends USSDController
{

    public function index(Request $request)
    {

        try {
            //msisdn=260761028631&subscriberInput=2106&sessionId=16790307700731726&isnewrequest=1 
            //Extract Target Client
                $mtnParams['subscriberInput'] = \strtoupper(\trim($request->input('subscriberInput')));
                $mtnParams['isNewRequest'] =$request->input('isnewrequest');
                $mtnParams['mobileNumber'] = $request->input('msisdn');
                $mtnParams['sessionId'] = $request->input('sessionId');
                $mtnParams['urlPrefix'] = $this->getUrlPrefix($request);
                $mtnParams['clean'] = $request->input('clean','');
                $mtnParams['mnoName'] = 'MTN';
                $this->theDTO=$this->theDTO->fromArray($mtnParams);
            //Process the Request
            $this->theDTO = $this->theService->handle($this->theDTO);
        } catch (\Throwable $e) {
            $this->theDTO->error = 'Error: At MTN controller level. '.$e->getMessage();
            $this->theDTO->response = \env('ERROR_MESSAGE');
            $this->theDTO->lastResponse = true;
        }
        return $this->responder($request);

    }
    
}
