<?php

namespace App\Http\Services\USSD\CouncilPaymentSpoofer;

use App\Http\DTOs\BaseDTO;

class CouncilPaymentSpoofer_Step_5
{

	public function run(BaseDTO $txDTO)
	{

		try {
			$txDTO->reference = $txDTO->subscriberInput;
			$txDTO->response="Enter Amount :\n";
		} catch (\Throwable $e) {
			$txDTO->error = 'Council proxy payment step 5. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
		
	}

}