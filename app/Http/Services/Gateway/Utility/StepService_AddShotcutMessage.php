<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Web\Sessions\ShortcutCustomerService;
use App\Http\DTOs\BaseDTO;
use Exception;

class StepService_AddShotcutMessage
{

	public function __construct(
		private ShortcutCustomerService $shortcutCustomerService)
	{}

	public function handle (BaseDTO $paymentDTO)
	{

		try {
			//Records in Cutomers Table UNIQUE on Phone_Number
			$customer = $this->shortcutCustomerService->findOneBy([
							'client_id'=>$paymentDTO->client_id,
							'mobileNumber'=>$paymentDTO->mobileNumber
						]);
			if (!$customer) {
				//Create Record
				$this->shortcutCustomerService->create([
						'client_id'=> $paymentDTO->client_id,
						'accountNumber' => $paymentDTO->accountNumber,
						'mobileNumber' => $paymentDTO->mobileNumber,
					]);
			}
			//Notify customer about Shortcut
			$arrCustomerJourney= \explode("*", $paymentDTO->customerJourney);
			$paymentDTO->receipt .= "Dial *".$arrCustomerJourney[0]."*".
										$arrCustomerJourney[1]."*(amount)# to pay.". "\n";
		} catch (\Throwable $e) {
			throw new Exception("Shotcut message not added! ".$e->getMessage());
		}
		return $paymentDTO;

	}

}