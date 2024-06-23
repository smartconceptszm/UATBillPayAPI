<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_LogStatus extends EfectivoPipelineContract
{

	protected function stepProcess(BaseDTO $paymentDTO)
	{
		
		try {
			if($paymentDTO->error==''){
				$logMessage='('.$paymentDTO->urlPrefix.'). Payment Status: '.
									$paymentDTO->paymentStatus." (via ".$paymentDTO->walletHandler."). ";
				if($paymentDTO->paymentStatus=='RECEIPTED' || 
					$paymentDTO->paymentStatus=='RECEIPT DELIVERED')
				{
					$logMessage.='DETAILS: '.$paymentDTO->receipt;
				}
				$logMessage.='Transaction ID = '.$paymentDTO->transactionId. '. - Session: '.$paymentDTO->sessionId.
				'. - Channel: '.$paymentDTO->channel.' - Wallet: '.$paymentDTO->walletNumber;
				Log::info($logMessage);
				if($paymentDTO->sms){
					if($paymentDTO->sms['status'] == 'DELIVERED'){
							Log::info('('.$paymentDTO->urlPrefix.') '.'SMS Notification SENT. Session: '.
											$paymentDTO->sessionId.' - Phone: '.$paymentDTO->mobileNumber); 
					}else{
							Log::error('('.$paymentDTO->urlPrefix.') SMS Notification NOT SENT. Details: '.$paymentDTO->sms['error'].
								'. Session: '.$paymentDTO->sessionId.' - Phone: '.$paymentDTO->mobileNumber); 
					}
				}
			}else{
				Log::error('('.$paymentDTO->urlPrefix.'). '.$paymentDTO->error.' Payment Status: '
					.$paymentDTO->paymentStatus.' (via '.$paymentDTO->walletHandler.'). Transaction ID = '.$paymentDTO->transactionId.
					'- Session: '.$paymentDTO->sessionId.'- Channel: '.$paymentDTO->channel.' - Wallet: '.$paymentDTO->walletNumber);
			}
		} catch (\Throwable $e) {
			$paymentDTO->error='At logging transaction. '.$e->getMessage();
		}

		return $paymentDTO;

	}

}