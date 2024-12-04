<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\USSD\StepServices\CheckPaymentsEnabled;
use App\Http\Services\USSD\StepServices\ValidateCRMInput;
use App\Http\Services\USSD\StepServices\GetRevenueCollectionDetails;
use App\Http\Services\USSD\StepServices\ConfirmToPay;
use App\Http\Services\Clients\MnoService;
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPayment_Step_6
{

	public function __construct(
		private GetRevenueCollectionDetails $getRevenuePointAndCollector,
		private CheckPaymentsEnabled $checkPaymentsEnabled,
      private ValidateCRMInput $validateInput,
		private ConfirmToPay $confirmToPay,
		private MnoService $mnoService)
	{}

	public function run(BaseDTO $txDTO)
	{

		try {     

         $txDTO->subscriberInput = $this->validateInput->handle('MOBILE',$txDTO->subscriberInput);
			$mnoName = MNOs::getMNO(substr($txDTO->subscriberInput,0,5));
			if($mnoName != $txDTO->mnoName){
				$mno = $this->mnoService->findOneBy(['name'=>$mnoName]); 
				$txDTO->payments_provider_id = $mno->payments_provider_id;
				$txDTO->mnoName = $mno->name;
				$txDTO->mno_id = $mno->id; 
				$paymentProviderStatus = $this->checkPaymentsEnabled->handle($txDTO);
				if(!$paymentProviderStatus['enabled']){
					throw new Exception($paymentProviderStatus['responseText'], 2);
				}
			}
			$txDTO->mobileNumber = $txDTO->subscriberInput;
         $customerJourney = \explode("*", $txDTO->customerJourney);
			\array_pop($customerJourney);
			$txDTO->subscriberInput = '1';
         $txDTO = $this->getRevenuePointAndCollector->handle($txDTO);
			$txDTO = $this->confirmToPay->handle($txDTO);
      } catch (\Throwable $e) {
			switch ($e->getCode()) {
				case 1:
					$txDTO->errorType = 'InvalidAmount';
					break;
				case 2:
					$txDTO->errorType = 'WalletNotActivated';
					break;
				default:
					$txDTO->errorType = 'SystemError';
					break;
			}
         $txDTO->error = $e->getMessage();
         return $txDTO;
      }
		return $txDTO; 

	}

}