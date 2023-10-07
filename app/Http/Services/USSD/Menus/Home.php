<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class Home implements IUSSDMenu
{

	public function __construct(
		private ClientMenuService $clientMenuService) 
	{}

	public function handle(BaseDTO $txDTO):BaseDTO
	{
		
		if($txDTO->error==''){
			try {
					$menus = $this->clientMenuService->findAll([
									'client_id'=>$txDTO->client_id,
									'parent_id'=>$txDTO->menu_id,
									'isActive' => 'YES'
								]);
					$prompt = $txDTO->menuPrompt.". Enter\n";
					foreach ($menus as $menu) {
						$prompt .= $menu->order.". ".$menu->prompt."\n";
					}
					$prompt .= "\n";
					$txDTO->response = $prompt;
			} catch (Exception $e) {
					$txDTO->error = 'At handle new session. '.$e->getMessage();
					$txDTO->errorType = 'SystemError';
			}  
		} 
		return $txDTO;

	}   

}