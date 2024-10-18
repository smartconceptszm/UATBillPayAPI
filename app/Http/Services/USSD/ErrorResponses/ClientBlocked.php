<?php

namespace App\Http\Services\USSD\ErrorResponses;

use App\Http\Services\USSD\ErrorResponses\IErrorResponse;
use App\Http\DTOs\BaseDTO;

class ClientBlocked implements IErrorResponse
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{

		try {    
			$billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
			$txDTO->response = $billpaySettings['BLOCKED_MESSAGE']." ".
										\strtoupper($txDTO->urlPrefix);
			$txDTO->error=$txDTO->response; 
			$txDTO->lastResponse= true;
		} catch (\Throwable $e) {
			$txDTO->error = 'At Generate maintenance mode response. '.$e->getMessage();
		}
		return $txDTO;
		
	}

}