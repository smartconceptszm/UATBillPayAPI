<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\USSD\SessionService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_SaveSession extends EfectivoPipelineContract
{

	public function __construct(
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
			if(!$txDTO->error){
				$txDTO->response = \env('ERROR_MESSAGE');
				$txDTO->lastResponse = true;
			}
		}
		return $txDTO;
		
	}
    
}