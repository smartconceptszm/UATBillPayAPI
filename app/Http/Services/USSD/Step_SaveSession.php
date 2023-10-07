<?php

namespace App\Http\Services\USSD;

use App\Http\Services\USSD\ErrorResponses\ErrorResponseBinderService;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\USSD\SessionService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_SaveSession extends EfectivoPipelineContract
{

	public function __construct(
		private ErrorResponseBinderService $errorResponseBinder,
		private SessionService $sessionService)
	{}

	protected function stepProcess(BaseDTO $txDTO)
	{
		
		try {
			if( $txDTO->customerJourney){
				$txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
			}else{
				$txDTO->customerJourney = $txDTO->subscriberInput;
			}
			$txDTO->error = $txDTO->error? \substr($txDTO->error,0,255):'';
			$this->sessionService->update($txDTO->toSessionData(),$txDTO->id);
		} catch (Exception $e) {
			$txDTO->error='At save session. '.$e->getMessage();
			$txDTO->errorType = 'SystemError';        
		}
		//Bind error response service to interface
		$txDTO->errorType = $txDTO->error? $txDTO->errorType:"NoError";   
		$this->errorResponseBinder->bind($txDTO->errorType);

		return $txDTO;
		
	}
    
}