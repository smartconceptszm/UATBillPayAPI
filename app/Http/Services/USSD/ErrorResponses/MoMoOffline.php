<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class MoMoOffline implements IErrorResponse
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		try {    
			$txDTO->lastResponse = true;
		} catch (\Throwable $e) {
			$txDTO->error = 'At Generate momo offline response. '.$e->getMessage();
		}
		return $txDTO;
		
	}

}