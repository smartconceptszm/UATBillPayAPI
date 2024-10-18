<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class MaintenanceMode implements IErrorResponse
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		try {   
			$billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true); 
			$txDTO->response = $billpaySettings['MODE_MESSAGE'];
			$txDTO->error=$txDTO->response;
			$txDTO->lastResponse = true;
		} catch (\Throwable $e) {
			$txDTO->error = 'At Generate maintenance mode response. '.$e->getMessage();
		}
		return $txDTO;
		
	}

}