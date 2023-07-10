<?php

namespace App\Http\BillPay\Services\USSD\CheckBalance;

use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\DTOs\BaseDTO;

class CheckBalance_SubStep_3 extends EfectivoPipelineWithBreakContract
{
    private $getCustomerAccount;
    public function __construct(StepService_GetCustomerAccount $getCustomerAccount){
        $this->getCustomerAccount=$getCustomerAccount;
    }
    
    protected function stepProcess(BaseDTO $txDTO)
    {

        if (\count(\explode("*", $txDTO->customerJourney)) == 3) {
            $txDTO->stepProcessed=true;
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber=$txDTO->subscriberInput;
            try {
                $txDTO->customer=$this->getCustomerAccount->handle($txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
            } catch (\Throwable $e) {
                if($e->getCode()==1){
                    $txDTO->errorType = "InvalidAccount";
                }else{
                    $txDTO->errorType = 'SystemError';
                }
                $txDTO->error = $e->getMessage();
                return $txDTO;
            }
            $txDTO->response="Acc: ".$txDTO->subscriberInput."\n". 
                        "Name: ".$txDTO->customer['name']."\n".
                        "Addr: ".$txDTO->customer['address']."\n". 
                        "Bal: ".$txDTO->customer['balance']."\n\n".

                        "Enter\n";
            if(\env(\strtoupper($txDTO->urlPrefix).'_'.$txDTO->mnoName.'_ACTIVE')=='YES'){
                $txDTO->response .= "1. To Pay Bill (via ".$txDTO->mnoName." Money)"."\n";
            }
            $txDTO->response.="0. Back";  
            $txDTO->status='COMPLETED';
        }
        return $txDTO;
        
    }
    
}