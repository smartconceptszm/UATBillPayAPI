<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;

class Step_HandleMenu extends EfectivoPipelineContract
{

	public function __construct(
		private IUSSDMenu $ussdMenu)
	{}
	
	protected function stepProcess(BaseDTO $txDTO)
	{

		try {

			$txDTO = $this->ussdMenu->handle($txDTO);   

		} catch (\Throwable $e) {
			$txDTO->error = 'At handle menu option. '.$e->getMessage();
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}
		return $txDTO;

	}

}