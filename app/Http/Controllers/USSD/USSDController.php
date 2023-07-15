<?php

namespace App\Http\Controllers\USSD;

use App\Http\BillPay\Services\USSD\USSDService;
use App\Http\Controllers\Contracts\Controller;
use App\Http\BillPay\DTOs\UssdDTO;
use Illuminate\Http\Request;

class USSDController extends Controller
{

    protected $theService;
    protected $theDTO;
    public function __construct(USSDService $theService, UssdDTO $theDTO)
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
        $request->merge(['ussdParams' =>$this->theDTO->toArray()]);
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
