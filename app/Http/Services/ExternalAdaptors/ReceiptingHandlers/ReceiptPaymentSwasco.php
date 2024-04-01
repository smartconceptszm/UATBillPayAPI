<?php

namespace App\Http\Services\ExternalAdaptors\ReceiptingHandlers;

use App\Http\Services\MoMo\Utility\StepService_AddShotcutMessage;
use App\Http\Services\ExternalAdaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;


class ReceiptPaymentSwasco implements IReceiptPayment
{

	public function __construct(
		private StepService_AddShotcutMessage $addShotcutMessage,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		//Trimmed to 20 cause of constraint on API
		$swascoTransactionRef = \strlen($momoDTO->sessionId) > 20 ? 
												\substr($momoDTO->sessionId, 0, 20) : $momoDTO->sessionId;
		$newBalance = "0";
		if($momoDTO->customer){
			if(\key_exists('balance',$momoDTO->customer)){
				$newBalance = (float)(\str_replace(",", "", $momoDTO->customer['balance'])) - 
												(float)$momoDTO->receiptAmount;
				$newBalance = \number_format($newBalance, 2, '.', ',');
			}
		}

		$receiptingParams=[ 
				'paymentType'=>"1",
				'account' => $momoDTO->accountNumber,
				'amount' => $momoDTO->receiptAmount,
				'mobileNumber'=> $momoDTO->mobileNumber,
				'referenceNumber' => $swascoTransactionRef,
		];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);

		if($billingResponse['status']=='SUCCESS'){
			$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
			$momoDTO->paymentStatus = "RECEIPTED";

			$momoDTO->receipt = "Payment successful\n" .
			"Rcpt No: " . $momoDTO->receiptNumber . "\n" .
			"Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
			"Acc: " . $momoDTO->accountNumber . "\n";
			if($newBalance != "0"){
				$momoDTO->receipt.="Bal: ZMW ". $newBalance . "\n";
			}
			$momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";

			//Handle shortcut
			if($momoDTO->customer){
				if(\key_exists('mobileNumber',$momoDTO->customer)){
					if ((($momoDTO->mobileNumber == $momoDTO->customer['mobileNumber']) && ($momoDTO->channel == 'USSD'))){
						try {
							$momoDTO = $this->addShotcutMessage->handle($momoDTO);
						} catch (\Throwable $e) {
							Log::error('('.$momoDTO->urlPrefix.'). '.$e->getMessage().
								'- Session: '.$momoDTO->sessionId.' - Phone: '.$momoDTO->mobileNumber);
						}
					}
				}
			}
		}else{
			$momoDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $momoDTO;

	}

}