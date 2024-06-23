<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;

class CheckBalance_Step_1 
{


	public function __construct(
		private StepService_AccountNoMenu $accountNoMenu
	){}

	public function run(BaseDTO $txDTO)
	{

		try {
			$txDTO->response = $this->accountNoMenu->handle($txDTO);
		} catch (\Throwable $e) {
			$txDTO->error = 'At check balance step 1. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
		
	}

}