<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\USSD\StepServices\ValidateCRMInput;
use App\Http\Services\USSD\StepServices\ConfirmToPay;
use App\Http\Services\Web\Clients\MnoService;
use App\Http\Services\Enums\MNOs;
use App\Http\DTOs\BaseDTO;

class CouncilPayment_Step_6
{

	public function __construct(
      private ValidateCRMInput $validateInput,
		private ConfirmToPay $confirmToPay,
		private MnoService $mnoService)
	{}

	public function run(BaseDTO $txDTO)
	{

		try {     

         $txDTO->subscriberInput = $this->validateInput->handle('MOBILE',$txDTO->subscriberInput);
			$mnoName = MNOs::getMNO(substr($txDTO->subscriberInput,0,5));
         $mno = $this->mnoService->findOneBy(['name'=>$mnoName]);  
			if($mnoName != $mno->name){
				$txDTO->payments_provider_id = $mno->payments_provider_id;
				$txDTO->mnoName = $mno->name;
				$txDTO->mno_id = $mno->id;
			}           
         $customerJourney = \explode("*", $txDTO->customerJourney);
			\array_pop($customerJourney);
			$txDTO->subscriberInput = '1';
			$txDTO = $this->confirmToPay->handle($txDTO);

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = 'InvalidAmount';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error = 'Council payment step 4. '. $e->getMessage();
         return $txDTO;
      }
		return $txDTO; 

	}

}