<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\External\Adaptors\BillingEnquiryHandlers\IEnquiryHandler;
use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Web\Clients\PaymentsProviderService;
use App\Http\DTOs\BaseDTO;

class CheckBalance_Step_2 
{

	public function __construct(
        private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
		  private PaymentsProviderService $paymentsProviderService,
        private IEnquiryHandler $getCustomerAccount)
	{}
	
	public function run(BaseDTO $txDTO)
	{

        	try {
				$txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
				if($txDTO->accountType == 'POST-PAID'){
					$txDTO->accountNumber=$txDTO->subscriberInput;
				}else{
					$txDTO->meterNumber=$txDTO->subscriberInput;
				}
				try {
					$txDTO = $this->getCustomerAccount->handle($txDTO);
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
				$paymentsProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
				if($paymentsProviderStatus['enabled']){
					$paymentsProvider = $this->paymentsProviderService->findById($txDTO->payments_provider_id);
					if($txDTO->accountType == 'POST-PAID'){
						$txDTO->response .= "1. To Pay Bill (via ".$paymentsProvider->shortName.")"."\n";
					}else{
						$txDTO->response .= "1. To Buy Units (via ".$paymentsProvider->shortName.")"."\n";
					}
				}
				$txDTO->response .= "2. Payments history\n";
				$txDTO->response .= "0. Back";  
				$txDTO->status = 'COMPLETED';
			} catch (\Throwable $e) {
					$txDTO->error = 'At check balance step 2. '.$e->getMessage();
					$txDTO->errorType = 'SystemError';
			}
		return $txDTO;
		
	}
    
}