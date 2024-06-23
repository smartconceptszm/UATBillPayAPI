<?php

namespace App\Http\Services\USSD\Menus;

use App\Http\Services\USSD\Utility\StepService_CheckPaymentsEnabled;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\Services\USSD\Menus\IUSSDMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class ParentMenu implements IUSSDMenu
{

	public function __construct(
		private StepService_CheckPaymentsEnabled $checkPaymentsEnabled,
		private ClientMenuService $clientMenuService) 
	{}

	public function handle(BaseDTO $txDTO):BaseDTO
	{
		
		if($txDTO->error==''){
			try {
				if($txDTO->isPayment=='YES'){
					$momoPaymentStatus = $this->checkPaymentsEnabled->handle($txDTO);
					if(!$momoPaymentStatus['enabled']){
						throw new Exception($momoPaymentStatus['responseText'], 1);
					}
				}
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
			} catch (\Throwable $e) {
				if($e->getCode() == 1) {
					$txDTO->error = $e->getMessage();
					$txDTO->errorType = 'PaymentProviderNotActivated';
				}else{
					$txDTO->error = 'At handle parent menu. '.$e->getMessage();
					$txDTO->errorType = 'SystemError';
				}
			}  
		} 
		return $txDTO;

	}   

}