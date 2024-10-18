<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

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