<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_SubStep_3 extends EfectivoPipelineWithBreakContract
{

	public function __construct(private GetCustomerAccount $getCustomerAccount)
	{}
	
	protected function stepProcess(BaseDTO $txDTO)
	{

		if (\count(\explode("*", $txDTO->customerJourney)) == 3) {

			$txDTO->stepProcessed = true;
			try {
				$txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber=$txDTO->subscriberInput;
            try {
                $txDTO->customer=$this->getCustomerAccount->handle($txDTO->accountNumber,$txDTO->urlPrefix);
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
			} catch (Exception $e) {
				$txDTO->error = 'At check balance step 3. '.$e->getMessage();
				$txDTO->errorType = 'SystemError';
			}
		}
		return $txDTO;
		
	}
    
}