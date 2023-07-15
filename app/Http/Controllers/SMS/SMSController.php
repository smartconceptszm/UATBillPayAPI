<?php

namespace App\Http\Controllers\SMS;

use App\Http\BillPay\Services\SMS\SMSService;
use App\Http\Controllers\Contracts\Controller;
use App\Http\BillPay\DTOs\SMSTxDTO;
use Illuminate\Http\Request;

class SMSController extends Controller
{

    private $smsService;
    private $dto;
    public function __construct(SMSService $smsService, SMSTxDTO $dto)
    { 
        $this->smsService = $smsService;
        $this->dto = $dto;
    }

    public function store(Request  $request)
    {

        try {
            $this->validate($request, $this->dto->validationRules);
            $this->dto = $this->dto->fromArray($request->all());
            $this->response['data'] = $this->smsService->send($this->dto);
        } catch (\Exception $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json($this->response);

    }

    

}
