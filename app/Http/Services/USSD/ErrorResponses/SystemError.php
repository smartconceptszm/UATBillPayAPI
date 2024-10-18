<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class SystemError implements IErrorResponse
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		try {    
			$billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
			$txDTO->response = $billpaySettings['ERROR_MESSAGE'];
			$txDTO->lastResponse = true;
		} catch (\Throwable $e) {
			$txDTO->error = 'At Generate system error response. '.$e->getMessage();
		}
		return $txDTO;
		
	}

}