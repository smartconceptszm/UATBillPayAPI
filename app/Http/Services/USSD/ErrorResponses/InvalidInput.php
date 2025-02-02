<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class InvalidInput implements IErrorResponse
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		try {    
			$txDTO->response = $txDTO->error;
			$txDTO->lastResponse = true;
		} catch (\Throwable $e) {
			$txDTO->error = 'At Generate invalid input response. '.$e->getMessage();
		}
		return $txDTO;
	}

}