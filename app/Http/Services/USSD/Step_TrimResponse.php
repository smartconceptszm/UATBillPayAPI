<?php

namespace App\Http\Services\USSD;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_TrimResponse extends EfectivoPipelineContract
{

	protected function stepProcess(BaseDTO $txDTO)
	{
		
		if($txDTO->error == ''){
			try {
				if(\strlen($txDTO->response)>159){
					$txDTO->response = $this->trim($txDTO->response,$txDTO->sessionId);
				}else{
					Cache::forget($txDTO->sessionId."handleNext");
				}
			} catch (Exception $e) {
				$txDTO->error = 'At trim response. '.$e->getMessage();
				$txDTO->errorType = 'SystemError';
			}
		}
		return $txDTO;
		
	}

	private function trim(string $response, string $sessionId):string
	{
		
		$strFirstSegment = \substr($response,0,145);
		$lastSegment = '';
		$positionOfLastPeriod = \strrpos($strFirstSegment,'.');
		if($positionOfLastPeriod){
			if(\is_numeric(\substr($strFirstSegment,$positionOfLastPeriod-1,1))){
					$strFirstSegment = \substr($strFirstSegment,0,$positionOfLastPeriod-2);
					$lastSegment = \substr($response,$positionOfLastPeriod-1);
			}else{
					$strFirstSegment = \substr($strFirstSegment,0,$positionOfLastPeriod);
					$lastSegment=\substr($response,$positionOfLastPeriod);
			}
		}else{
			$lastSegment=\substr($response,144);
		}

		$strFirstSegment.="\n00 Next";
		if(\strlen($strFirstSegment)<152){
			$strFirstSegment.="\n0 Back\n";
			$cacheValue = \json_encode([
						'must'=>false,
						'steps'=>1,
					]);
			Cache::put($sessionId."handleBack",$cacheValue, 
					Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
		}
		if(\strlen($lastSegment)<153){
			// if(!(\strpos($lastSegment,"Back"))){
			//     $lastSegment.="\n0 Back\n";
			// }
		}

		Cache::put($sessionId."responseNext",$lastSegment, 
							Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
		return $strFirstSegment;

	}

}

