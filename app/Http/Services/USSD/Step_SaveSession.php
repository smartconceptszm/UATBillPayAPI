<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Sessions\SessionService;
use Illuminate\Support\Facades\Cache;
use App\Http\DTOs\BaseDTO;

use function PHPUnit\Framework\isEmpty;

class Step_SaveSession extends EfectivoPipelineContract
{

	public function __construct(
		private SessionService $sessionService)
	{}

	protected function stepProcess(BaseDTO $txDTO)
	{
		
		try {
			if(isset($txDTO->customerJourney) && $txDTO->customerJourney !== ''){
				$txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
			}else{
				$txDTO->customerJourney = $txDTO->subscriberInput;
			}
			$this->sessionService->update($txDTO->toSessionData(),$txDTO->id);
		} catch (\Throwable $e) {
			$billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
			$txDTO->error='At save session. '.$e->getMessage();
			if(!$txDTO->error){
				$txDTO->response = $billpaySettings['ERROR_MESSAGE'];
				$txDTO->lastResponse = true;
			}
		}
		return $txDTO;
		
	}
    
}