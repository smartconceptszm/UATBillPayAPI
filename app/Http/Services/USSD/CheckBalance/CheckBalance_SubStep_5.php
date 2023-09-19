<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;

class CheckBalance_SubStep_5 extends EfectivoPipelineWithBreakContract
{

	protected function stepProcess(BaseDTO $txDTO)
	{
		
		if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
			$txDTO->stepProcessed=true;
			$txDTO->error="Duplicated request from ".$txDTO->mnoName.
											" with input: ".$txDTO->subscriberInput; 
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
		
	}

}