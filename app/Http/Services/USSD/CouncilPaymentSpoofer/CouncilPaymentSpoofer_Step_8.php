<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\DTOs\BaseDTO;

class CouncilPaymentSpoofer_Step_8
{

	public function run(BaseDTO $txDTO)
	{
		if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
			$txDTO->error = "Duplicated request from ".$txDTO->mnoName.
											" with input: ".$txDTO->subscriberInput; 
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;        
	}

}