<?php

namespace App\Http\Services\USSD\GetTokens;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class GetTokens_Step_1 
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
			$txDTO->error = 'At get token step 1. '.$e->getMessage();
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}
		return $txDTO;
		
	}

}