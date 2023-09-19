<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class NoError implements IErrorResponse
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		$txDTO->error = "";
		return $txDTO;
		
	}
	
}