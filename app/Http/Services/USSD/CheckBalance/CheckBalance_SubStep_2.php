<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class CheckBalance_SubStep_2 extends EfectivoPipelineWithBreakContract
{

	public function __construct( private StepService_AccountNoMenu $accountNoMenu)
	{}

	protected function stepProcess(BaseDTO $txDTO)
	{

		if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
			$txDTO->stepProcessed = true;
			try {
				if($txDTO->subscriberInput != "1" && $txDTO->subscriberInput != "2"){
					$txDTO->error = "Invalid input";
					$txDTO->errorType = "InvalidInput";
					return $txDTO;
				}
				$prePaidText = $txDTO->subscriberInput=="2"? "PRE-PAID ":"";
				$txDTO->response = $this->accountNoMenu->handle($prePaidText,$txDTO->urlPrefix);
			} catch (Exception $e) {
				$txDTO->error = 'At check balance step 2. '.$e->getMessage();
				$txDTO->errorType = 'SystemError';
			}
		}
		return $txDTO;
		
	}
}