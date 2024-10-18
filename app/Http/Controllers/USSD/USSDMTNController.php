<?php

namespace App\Http\Controllers\USSD;

use App\Http\Controllers\USSD\USSDController;
use Illuminate\Support\Facades\Cache;
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
                $mno = $this->getMno($mtnParams['mnoName']);
                $mtnParams['payments_provider_id'] = $mno->payments_provider_id;
                $mtnParams['mno_id'] = $mno->id; 
                $this->ussdDTO=$this->ussdDTO->fromArray($mtnParams);
            //Process the Request
            $this->ussdDTO = $this->ussdService->handle($this->ussdDTO);
        } catch (\Throwable $e) {
            $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
            $this->ussdDTO->error = 'Error: At MTN controller level. '.$e->getMessage();
            $this->ussdDTO->response =$billpaySettings['ERROR_MESSAGE'];
            $this->ussdDTO->lastResponse = true;
        }
        return $this->responder($request);

    }
    
}
