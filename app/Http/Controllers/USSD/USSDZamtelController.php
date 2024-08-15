<?php

namespace App\Http\Controllers\USSD;

use App\Http\Controllers\USSD\USSDController;
use Illuminate\Http\Request;

class USSDZamtelController extends USSDController
{

    public function index(Request $request)
    {

        try {
            // /swasco/Zamtel?&TransId=17BBB0D8480&Pid=0&RequestType=1&MSISDN=260956099652&SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757%23

            //Parse Parameters
            if ($request->input('RequestType') == '1') {
                $theInput = substr_replace($request->input('USSDString'), '', -1);
                $zamtelParams['subscriberInput'] = substr($theInput, 1);
                // $zamtelParams['subscriberInput']= $request->input('SHORTCODE');
                $zamtelParams['isNewRequest']= '1';
            }else{
                $theInput = \explode("*", $request->input('USSDString'));
                $zamtelParams['subscriberInput']= \strtoupper($theInput[count($theInput) - 1]);
                $zamtelParams['isNewRequest']= '0';
                if ($request->input('RequestType') == '3') {
                    $zamtelParams['clean'] ="clean-session";
                }
            }
            $zamtelParams['mobileNumber'] = $request->input('MSISDN');
            $zamtelParams['urlPrefix']=$this->getUrlPrefix($request);
            $zamtelParams['sessionId'] = $request->input('TransId');
            $zamtelParams['mnoName'] = 'ZAMTEL';
            $mno = $this->getMno($zamtelParams['mnoName']);
            $zamtelParams['payments_provider_id'] = $mno->payments_provider_id;
            $zamtelParams['mno_id'] = $mno->id; 
            $this->ussdDTO=$this->ussdDTO->fromArray($zamtelParams);
            //Process the Request
            $this->ussdDTO = $this->ussdService->handle($this->ussdDTO);

            //Format the Response Text
            $responseText = '';
            $responseText = '&TransId=' . $this->ussdDTO->sessionId;
            $responseText .= '&Pid=' . $request->input('Pid');
            if ($this->ussdDTO->lastResponse) {
                $responseText .= '&RequestType=3';
            } else {
                $responseText .= '&RequestType=2';
            }
            $responseText .= '&MSISDN=' . $request->input('MSISDN');
            $responseText .= '&AppId=' . $request->input('AppId');
            $responseText .= '&USSDString=' . $this->ussdDTO->response;
            $this->ussdDTO->response=$responseText;
        } catch (\Throwable $e) {
            $this->ussdDTO->error = 'Error: At Zamtel controller level. '.$e->getMessage();
            $this->ussdDTO->response = \env('ERROR_MESSAGE');
            $this->ussdDTO->lastResponse = true;
        }
        return $this->responder($request);

    }
    
}
