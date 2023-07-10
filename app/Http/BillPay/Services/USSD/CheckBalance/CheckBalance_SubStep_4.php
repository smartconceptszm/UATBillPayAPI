<?php

namespace App\Http\BillPay\Services\USSD\CheckBalance;

use App\Http\BillPay\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\DTOs\BaseDTO;

class CheckBalance_SubStep_4 extends EfectivoPipelineWithBreakContract
{

    private $checkPaymentsEnabled;
    private $accountNoMenu;
    public function __construct(StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
        StepService_AccountNoMenu $accountNoMenu
    ){
        $this->checkPaymentsEnabled=$checkPaymentsEnabled;
        $this->accountNoMenu=$accountNoMenu;
    }

    protected function stepProcess(BaseDTO $txDTO)
    {
        $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
        if(\count($arrCustomerJourney)==4){
            $txDTO->stepProcessed=true;
            if($txDTO->subscriberInput=='1'){
                $txDTO = $this->checkPaymentsEnabled->handle($txDTO);
                if(!$txDTO->response){
                    if($arrCustomerJourney[2]=='2'){
                        $txDTO->menu = "BuyUnits";
                    }else{
                        $txDTO->menu = "PayBill";
                    }
                    $txDTO->customerJourney=$arrCustomerJourney[0]."*".
                                        \config('efectivo_clients.'.$txDTO->urlPrefix.'.menu.'.$txDTO->menu);
                    $txDTO->subscriberInput=$arrCustomerJourney[3];
                    $txDTO->response="Enter Amount :\n";
                    $txDTO->status='INITIATED';
                }
                return $txDTO;
            }
            if($txDTO->subscriberInput=='0'){
                $txDTO->customerJourney =$arrCustomerJourney[0].'*'.$arrCustomerJourney[1];
                $txDTO->subscriberInput=$arrCustomerJourney[2];
                $prePaidText = $txDTO->subscriberInput=="2"? "PRE-PAID ":"";
                $txDTO->response=$this->accountNoMenu->handle($prePaidText,$txDTO->urlPrefix);
                $txDTO->status='INITIATED';
                return $txDTO;
            }

            $txDTO->accountNumber=$arrCustomerJourney[2];
            $txDTO->error = 'User entered invalid input';
            $txDTO->errorType= "InvalidInput";

        }
        return $txDTO;

    }

}