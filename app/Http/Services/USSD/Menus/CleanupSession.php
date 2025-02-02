<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class CleanupSession implements IUSSDMenu
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{
		
		if($txDTO->error==''){
			$txDTO->response = '';
			$txDTO->status = USSDStatusEnum::Completed->value;
			$txDTO->lastResponse = true;
		} 
		return $txDTO;

	}   

}