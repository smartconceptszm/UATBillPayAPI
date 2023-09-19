<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_SubStep_1 extends EfectivoPipelineWithBreakContract
{


	public function __construct(
		private StepService_AccountNoMenu $accountNoMenu
	){}

	protected function stepProcess(BaseDTO $txDTO)
	{

		if (\count(\explode("*", $txDTO->customerJourney)) == 1) {
			$txDTO->stepProcessed=true;
			try {
				if(\config('efectivo_clients.'.$txDTO->urlPrefix.'.hasPrepaid')){
					$txDTO->response =  "Enter\n". 
												"1. Post paid account\n".
												"2. Pre paid account\n";
				}else{
					$txDTO->customerJourney=$txDTO->customerJourney.'*'.
																		$txDTO->subscriberInput;
					$txDTO->subscriberInput = "1";
					$txDTO->response = $this->accountNoMenu->handle("",$txDTO->urlPrefix);
				}
			} catch (Exception $e) {
				$txDTO->error = 'At check balance step 1. '.$e->getMessage();
				$txDTO->errorType = 'SystemError';
			}
		}
		return $txDTO;
		
	}

}