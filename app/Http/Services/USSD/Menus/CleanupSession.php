<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;

class CleanupSession implements IUSSDMenu
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{
		
		if($txDTO->error==''){
			$txDTO->response = '';
			$txDTO->status = 'COMPLETED'; 
			$txDTO->lastResponse = true;
		} 
		return $txDTO;

	}   

}