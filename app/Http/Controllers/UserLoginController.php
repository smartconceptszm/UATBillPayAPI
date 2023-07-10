<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\UserLoginService;
use App\Http\BillPay\DTOs\UserLoginDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserLoginController  extends Controller
{

    private $theService;
    private $dto;
    public function __construct(UserLoginService $theService, UserLoginDTO $dto)
    { 
        $this->theService = $theService;
        $this->dto = $dto;
    }


    public function store(Request  $request)
    {

        try {
            $this->validate($request, $this->dto->validationRules);
            $this->dto = $this->dto->fromArray($request->all());
            $this->response['data']=$this->theService->create($this->dto);
        } catch (\Exception $e) {
            $this->response['status']['code']=500;
            $this->response['status']['message']=$e->getMessage();
        }
        return response()->json($this->response);

    }

}
