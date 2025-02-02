<?php

namespace App\Http\Services\USSD\MakePayment;

use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class MakePayment_Step_3
{

	public function run(BaseDTO $txDTO)
	{

		try {
			$txDTO->reference = $txDTO->subscriberInput;
			$txDTO->response="Enter Amount :\n";
		} catch (\Throwable $e) {
			$txDTO->error = 'Make payment step 3. '.$e->getMessage();
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}
		return $txDTO;
		
	}

}