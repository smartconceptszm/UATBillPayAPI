<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class CouncilPayment_Step_7
{

	public function run(BaseDTO $txDTO)
	{
		if (\count(\explode("*", $txDTO->customerJourney)) > 4) {
			$txDTO->error = "Duplicated request from ".$txDTO->mnoName.
											" with input: ".$txDTO->subscriberInput; 
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}
		return $txDTO;        
	}

}