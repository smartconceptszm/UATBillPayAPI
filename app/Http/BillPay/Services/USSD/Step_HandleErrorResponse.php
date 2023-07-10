<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;

use App\Http\BillPay\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_HandleErrorResponse extends EfectivoPipelineContract
{
    
    private $errorMenu;
    public function __construct(IErrorResponse $errorMenu)
    {
        $this->errorMenu = $errorMenu;
    }
    
    protected function stepProcess(BaseDTO $txDTO)
    {

        try {
            if($txDTO->error){
                $txDTO=$this->errorMenu->handle($txDTO);   
            }
        } catch (\Throwable $e) {
            $txDTO->error='At handle error response menu'.$e->getMessage();
        }
        return $txDTO;

    }


}