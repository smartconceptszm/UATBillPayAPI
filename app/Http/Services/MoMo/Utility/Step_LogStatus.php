<?php

namespace App\Http\Services\MoMo\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_LogStatus extends EfectivoPipelineContract
{

	protected function stepProcess(BaseDTO $momoDTO)
	{
		
		try {
			if($momoDTO->error==''){
				$logMessage='('.$momoDTO->clientCode.'). Payment Status: '.
									$momoDTO->paymentStatus." (via ".$momoDTO->mnoName."). ";
				if($momoDTO->paymentStatus=='RECEIPTED' || 
					$momoDTO->paymentStatus=='RECEIPT DELIVERED')
				{
					$logMessage.='DETAILS: '.$momoDTO->receipt;
				}
				$logMessage.='Transaction ID = '.$momoDTO->transactionId. '. - Session: '.$momoDTO->sessionId.
						' - Phone: '.$momoDTO->mobileNumber;
				Log::info($logMessage);
				if($momoDTO->sms){
					if($momoDTO->sms['status'] == 'DELIVERED'){
							Log::info('('.$momoDTO->clientCode.') '.'SMS Notification SENT. Session: '.
											$momoDTO->sessionId.' - Phone: '.$momoDTO->mobileNumber); 
					}else{
							Log::error('('.$momoDTO->clientCode.') SMS Notification NOT SENT. Details: '.$momoDTO->sms['error'].
								'. Queued for re-try. Session: '.$momoDTO->sessionId.' - Phone: '.$momoDTO->mobileNumber); 
					}
				}
			}else{
				Log::error('('.$momoDTO->clientCode.'). '.$momoDTO->error.' Payment Status: '
					.$momoDTO->paymentStatus.' (via '.$momoDTO->mnoName.'). Transaction ID = '.$momoDTO->transactionId.
					'- Session: '.$momoDTO->sessionId.' - Phone: '.$momoDTO->mobileNumber);
			}
		} catch (Exception $e) {
			$momoDTO->error='At logging transaction. '.$e->getMessage();
		}

		return $momoDTO;

	}

}