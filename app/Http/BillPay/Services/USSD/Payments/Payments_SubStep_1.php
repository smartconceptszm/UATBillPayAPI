<?php

namespace App\Http\BillPay\Services\USSD\Payments;

use App\Http\BillPay\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class Payments_SubStep_1 extends EfectivoPipelineWithBreakContract
{

    private $checkPaymentsEnabled;
    private $accountNoMenu;
    public function __construct(StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
        StepService_AccountNoMenu $accountNoMenu)
    {
        $this->checkPaymentsEnabled=$checkPaymentsEnabled;
        $this->accountNoMenu=$accountNoMenu;
    }

    protected function stepProcess(BaseDTO $txDTO)
    {

        if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
            $txDTO->stepProcessed=true;
            try {    
                $txDTO = $this->checkPaymentsEnabled->handle($txDTO);
                if(!$txDTO->response){
                    $prePaidText = $txDTO->menu === "BuyUnits" ? "PRE-PAID": "";
                    $txDTO->response = $this->accountNoMenu->handle($prePaidText,$txDTO->urlPrefix);
                }
            } catch (\Throwable $e) {
                $txDTO->error = 'Payments sub step 1. '.$e->getMessage();
                $txDTO->errorType = 'SystemError';
            }
        }
        return $txDTO;
        
    }
}