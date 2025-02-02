<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\Clients\PaymentsProviderService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class CheckBalance_Step_2 
{

	public function __construct(
        private CheckPaymentsEnabled $checkPaymentsEnabled,
		  private PaymentsProviderService $paymentsProviderService,
		  private ClientMenuService $clientMenuService,
		  private EnquiryHandler $enquiryHandler)
	{}
	
	public function run(BaseDTO $txDTO)
	{

        	try {
				$txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
				$clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
				$txDTO->customerAccount = $txDTO->subscriberInput;
				try {
					$txDTO = $this->enquiryHandler->handle($txDTO);
				} catch (\Throwable $e) {
						if($e->getCode()==1){
							$txDTO->errorType = USSDStatusEnum::InvalidAccount->value;
						}else{
							$txDTO->errorType = USSDStatusEnum::SystemError->value;
						}
						$txDTO->error = $e->getMessage();
						return $txDTO;
				}
				$txDTO->response = "Acc: ".$txDTO->subscriberInput."\n". 
											"Name: ".$txDTO->customer['name']."\n".
											"Addr: ".$txDTO->customer['address']."\n". 
											"Bal: ".$txDTO->customer['balance']."\n\n".
											"Enter\n";
				$paymentsProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
				if($paymentsProviderStatus['enabled']){
					$clientMenu = $this->clientMenuService->findOneBy([
																		'client_id' => $txDTO->client_id,
																		'isPayment' => 'YES',
																		'isDefault' => 'YES',
																		'isActive' => "YES",
																		'billingClient' =>  $clientMenu->billingClient
																	]);
					$paymentsProvider = $this->paymentsProviderService->findById($txDTO->payments_provider_id);
					$txDTO->response .= "1. ".$clientMenu->prompt." (via ".$paymentsProvider->shortName.")"."\n";
				}
				$txDTO->response .= "2. Payments history\n";
				$txDTO->response .= "0. Back";  
				$txDTO->status = USSDStatusEnum::Completed->value;
			} catch (\Throwable $e) {
					$txDTO->error = 'At check balance step 2. '.$e->getMessage();
					$txDTO->errorType = USSDStatusEnum::SystemError->value;
			}
		return $txDTO;
		
	}
    
}