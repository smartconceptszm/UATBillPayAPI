<?php

namespace App\Http\Controllers\USSD;

use App\Http\Controllers\USSD\USSDController;
use Illuminate\Http\Request;

class USSDZamtelController extends USSDController
{

    public function index(Request $request)
    {

        try {
            // /swasco/Zamtel?&TransId=17BBB0D8480&Pid=0&RequestType=1&MSISDN=260956099652
            // &SHORTCODE=5757&cellID=000000000000000&AppId=1&USSDString=*5757%23

            //Parse Parameters
            if ($request->input('RequestType') == '1') {
                $zamtelParams['subscriberInput']= $request->input('SHORTCODE');
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
            $this->theDTO=$this->theDTO->fromArray($zamtelParams);
            //Process the Request
            $this->theDTO = $this->theService->handle($this->theDTO);

            //Format the Response Text
            $responseText = '';
            $responseText = '&TransId=' . $this->theDTO->sessionId;
            $responseText .= '&Pid=' . $request->input('Pid');
            if ($this->theDTO->lastResponse) {
                $responseText .= '&RequestType=3';
            } else {
                $responseText .= '&RequestType=2';
            }
            $responseText .= '&MSISDN=' . $request->input('MSISDN');
            $responseText .= '&AppId=' . $request->input('AppId');
            $responseText .= '&USSDString=' . $this->theDTO->response;
            $this->theDTO->response=$responseText;
        } catch (\Throwable $e) {
            $this->theDTO->error = 'Error: At Zamtel controller level. '.$e->getMessage();
            $this->theDTO->response = \env('ERROR_MESSAGE');
            $this->theDTO->lastResponse = true;
        }
        return $this->responder($request);

    }
    
}
