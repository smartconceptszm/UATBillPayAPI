<?php

namespace App\Http\Services\USSD\PayBill;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class Payments_SubStep_2 extends EfectivoPipelineWithBreakContract
{

	protected function stepProcess(BaseDTO $txDTO)
	{

		if (\count(\explode("*", $txDTO->customerJourney)) == 2) {
			$txDTO->stepProcessed = true;
			try {
				$txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
				$txDTO->accountNumber = $txDTO->subscriberInput;
				$txDTO->response="Enter Amount :\n";
			} catch (Exception $e) {
				$txDTO->error = 'Pay bill sub step 2. '.$e->getMessage();
				$txDTO->errorType = 'SystemError';
			}

		}
		return $txDTO;
		
	}

}