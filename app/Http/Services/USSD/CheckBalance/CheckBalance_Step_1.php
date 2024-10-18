<?php

namespace App\Http\Services\USSD\CheckBalance;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;

class CheckBalance_Step_1 
{


	public function __construct(
		private ClientMenuService $clientMenuService
	){}

	public function run(BaseDTO $txDTO)
	{

		try {
			$clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
			$txDTO->response = "Enter ".$clientMenu->customerAccountPrompt.":\n";
		} catch (\Throwable $e) {
			$txDTO->error = 'At check balance step 1. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';
		}
		return $txDTO;
		
	}

}