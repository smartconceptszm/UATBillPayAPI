<?php

namespace App\Http\Services\USSD\MakePayment;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class MakePayment_Step_3
{

	public function __construct(
      private ClientMenuService $clientMenuService)
   {}

	public function run(BaseDTO $txDTO)
	{

		try {
			$clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
			$txDTO->reference = $txDTO->subscriberInput;
			if($clientMenu->amountPrompt){
				$txDTO->response = $clientMenu->amountPrompt.":\n";
			}else{
				$txDTO->response="Enter Amount :\n";
			}
		} catch (\Throwable $e) {
			$txDTO->error = 'Make payment step 3. '.$e->getMessage();
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}
		return $txDTO;
		
	}

}