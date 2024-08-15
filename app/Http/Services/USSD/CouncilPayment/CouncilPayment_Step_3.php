<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\DTOs\BaseDTO;

class CouncilPayment_Step_3
{

	public function run(BaseDTO $txDTO)
	{

		try {
			$txDTO->reference = $txDTO->subscriberInput;
			$txDTO->response="Enter Amount :\n";
		} catch (\Throwable $e) {
			$txDTO->error = 'Council payment step 3. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
		
	}

}