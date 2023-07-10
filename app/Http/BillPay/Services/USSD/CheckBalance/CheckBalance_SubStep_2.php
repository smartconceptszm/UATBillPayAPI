<?php

namespace App\Http\BillPay\Services\USSD\CheckBalance;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class CheckBalance_SubStep_2 extends EfectivoPipelineWithBreakContract
{
    private $accountNoMenu;
    public function __construct(StepService_AccountNoMenu $accountNoMenu)
    {
        $this->accountNoMenu=$accountNoMenu;
    }

    protected function stepProcess(BaseDTO $txDTO)
    {

        if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
            $txDTO->stepProcessed=true;
            if($txDTO->subscriberInput!="1" && $txDTO->subscriberInput!="2"){
                $txDTO->error= "Invalid input";
                $txDTO->errorType= "InvalidInput";
                return $txDTO;
            }
            $prePaidText = $txDTO->subscriberInput=="2"? "PRE-PAID ":"";
            $txDTO->response=$this->accountNoMenu->handle($prePaidText,$txDTO->urlPrefix);
        }
        return $txDTO;
        
    }
}