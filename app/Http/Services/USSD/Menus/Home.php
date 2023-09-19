<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class Home implements IUSSDMenu
{

	public function handle(BaseDTO $txDTO):BaseDTO
	{
		
		if($txDTO->error==''){
			try {
					$txDTO->response = \config('efectivo_clients.'.$txDTO->urlPrefix.'.Home');
			} catch (Exception $e) {
					$txDTO->error = 'At handle new session. '.$e->getMessage();
					$txDTO->errorType = 'SystemError';
			}  
		} 
		return $txDTO;

	}   

}