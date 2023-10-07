<?php

namespace App\Http\Services\MoMo\Utility;

use App\Http\Services\USSD\ShortcutCustomerService;
use App\Http\DTOs\BaseDTO;
use Exception;

class StepService_AddShotcutMessage
{

	public function __construct(
		private ShortcutCustomerService $shortcutCustomerService)
	{}

	public function handle (BaseDTO $momoDTO)
	{

		try {
			//Records in Cutomers Table UNIQUE on Phone_Number
			$customer = $this->shortcutCustomerService->findOneBy([
							'client_id'=>$momoDTO->client_id,
							'mobileNumber'=>$momoDTO->mobileNumber
						]);
			if ($customer->count() == 0) {
				//Create Record
				$this->shortcutCustomerService->create([
						'client_id'=> $momoDTO->client_id,
						'accountNumber' => $momoDTO->accountNumber,
						'mobileNumber' => $momoDTO->mobileNumber,
					]);
			}
			//Notify customer about Shortcut
			$arrCustomerJourney= \explode("*", $momoDTO->customerJourney);
			$momoDTO->receipt .= "Dial *".$arrCustomerJourney[0]."*".
										$arrCustomerJourney[1]."*(amount)# to pay";
		} catch (Exception $e) {
			throw new Exception("Shotcut message not added! ".$e->getMessage());
		}
		return $momoDTO;

	}

}