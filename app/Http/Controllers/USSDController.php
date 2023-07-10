<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\USSDSessionService;
use App\Http\BillPay\DTOs\UssdDTO;
use Illuminate\Http\Request;

class USSDController extends Controller
{

    protected $theService;
    protected $theDTO;
    public function __construct(USSDSessionService $theService, UssdDTO $theDTO)
    {
        $this->theService=$theService;
        $this->theDTO=$theDTO;
    }

    protected function getUrlPrefix(Request $request):string
    {
        $requestUrlArr=\explode("/",$request->url());
        $clientUrlPrefix=$requestUrlArr[\count($requestUrlArr)-2];
        return $clientUrlPrefix;
    }

    protected function responder(Request $request)
    {
        //For Terminate Middleware
        $request->request->add(['ussdParams' =>$this->theDTO->toArray()]);
        //Respond
        $theHeaders = $this->prepHeaders($this->theDTO);
        return response($this->theDTO->response,200)->withHeaders($theHeaders);
    }

    protected function prepHeaders(UssdDTO $txDTO): array
    {

        $theHeaders = ['Content-Type" => "text/plain'];
        if ($txDTO->lastResponse) {
            $theHeaders['Freeflow'] = "FB";
        } else {
            $theHeaders['Freeflow'] = "FC";
        }

        if ($txDTO->clean == "clean-session") {
            $theHeaders['Expires'] = "-1";
            $theHeaders['Pragma'] = "no-cache";
            $theHeaders['Cache-Control'] = "max-age=0";
        }
        return $theHeaders;
        
    }
    
}
