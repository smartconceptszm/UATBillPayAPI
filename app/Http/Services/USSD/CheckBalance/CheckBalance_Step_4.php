<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class CheckBalance_Step_4
{

	public function run(BaseDTO $txDTO)
	{
		
		if (\count(\explode("*", $txDTO->customerJourney)) > 3) {
			$txDTO->error="Duplicated request from ".$txDTO->mnoName.
											" with input: ".$txDTO->subscriberInput; 
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}
		return $txDTO;
		
	}

}