<?php

namespace App\Http\Services\USSD\PayBill;

use App\Http\DTOs\BaseDTO;

class PayBill_Step_2
{

	public function run(BaseDTO $txDTO)
	{

		try {
			$txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
			$txDTO->accountNumber = $txDTO->subscriberInput;
			$txDTO->response="Enter Amount :\n";
		} catch (\Throwable $e) {
			$txDTO->error = 'Pay bill sub step 2. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
		
	}

}