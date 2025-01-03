<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Gateway\Utility\StepService_AddShotcutMessage;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidSwasco implements IReceiptPayment
{

	public function __construct(
		private StepService_AddShotcutMessage $addShotcutMessage,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		//Trimmed to 20 cause of constraint on API
		$swascoTransactionRef = \strlen($paymentDTO->sessionId) > 20 ? 
												\substr($paymentDTO->sessionId, 0, 20) : $paymentDTO->sessionId;
		$newBalance = "0";
		if($paymentDTO->customer){
			if(\key_exists('balance',$paymentDTO->customer)){
				$newBalance = (float)(\str_replace(",", "", $paymentDTO->customer['balance'])) - 
												(float)$paymentDTO->receiptAmount;
				$newBalance = \number_format($newBalance, 2, '.', ',');
			}
		}

		$receiptingParams = [ 
										'referenceNumber' => $swascoTransactionRef,
										'account' => $paymentDTO->customerAccount,
										'amount' => $paymentDTO->receiptAmount,
										'paymentType'=>"01",
										'receiptType'=>"2",
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'client_id' => $paymentDTO->client_id
									];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);

		if($billingResponse['status']=='SUCCESS'){
			$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
			$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;

			$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format( $paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->customerAccount . "\n";
			if($newBalance != "0"){
				$paymentDTO->receipt.="Bal: ZMW ". $newBalance . "\n";
			}
			$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";

			//Handle shortcut
			if($paymentDTO->customer){
				if(\key_exists('mobileNumber',$paymentDTO->customer)){
					if ((($paymentDTO->mobileNumber == $paymentDTO->customer['mobileNumber']) && ($paymentDTO->channel == 'USSD'))){
						try {
							$paymentDTO = $this->addShotcutMessage->handle($paymentDTO);
						} catch (\Throwable $e) {
							Log::error('('.$paymentDTO->urlPrefix.'). '.$e->getMessage().
								'- Session: '.$paymentDTO->sessionId.' - Phone: '.$paymentDTO->mobileNumber);
						}
					}
				}
			}
		}else{
			$paymentDTO->error = "At receipt payment. ".$billingResponse['error'];
		}
		return $paymentDTO;

	}

}