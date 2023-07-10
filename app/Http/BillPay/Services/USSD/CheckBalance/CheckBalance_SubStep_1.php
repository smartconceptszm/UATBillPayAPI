<?php

namespace App\Http\BillPay\Services\USSD\CheckBalance;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class CheckBalance_SubStep_1 extends EfectivoPipelineWithBreakContract
{
    private $accountNoMenu;
    public function __construct(StepService_AccountNoMenu $accountNoMenu)
    {
        $this->accountNoMenu=$accountNoMenu;
    }
    protected function stepProcess(BaseDTO $txDTO)
    {

        if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
            $txDTO->stepProcessed=true;
            if(\config('efectivo_clients.'.$txDTO->urlPrefix.'.hasPrepaid')){
                $txDTO->response =  "Enter\n". 
                                    "1. Post paid account\n".
                                    "2. Pre paid account\n";
            }else{
                $txDTO->customerJourney=$txDTO->customerJourney.'*'.
                                                        $txDTO->subscriberInput;
                $txDTO->subscriberInput = "1";
                $txDTO->response=$this->accountNoMenu->handle("",$txDTO->urlPrefix);
            }
        }
        return $txDTO;
        
    }
}