<?php
namespace App\Http\Services\USSD\Menus;

use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Clients\ClientService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class ParentMenu implements IUSSDMenu
{

	public function __construct(
		private ClientMenuService $clientMenuService,
		private ClientService $clientService) 
	{}

	public function handle(BaseDTO $txDTO):BaseDTO
	{
		
		try {
			if($txDTO->error==''){
				$menus = $this->clientMenuService->findAll([
												'client_id'=>$txDTO->client_id,
												'parent_id'=>$txDTO->menu_id,
												'isActive' => 'YES'
											]);

				$prompt = $txDTO->menuPrompt."\n";
				foreach ($menus as $menu) {
					$prompt .= $menu->order.". ".$menu->prompt."\n";
				}
				$prompt .= "\n";
				$txDTO->response = $prompt;
			} 
		} catch (\Throwable $e) {
			$txDTO->error = 'At handle parent menu. '.$e->getMessage();
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
		}  
		return $txDTO;

	}   

}