<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\AbrReceiptMulonga;
use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\DTOs\BaseDTO;

class ReceiptVacuumTankerMulonga extends AbrReceiptMulonga implements IReceiptPayment
{

	public function handle(BaseDTO $paymentDTO):BaseDTO
	{

		//Trimmed to 20 cause of constraint on API
		$mulongaTransactionRef = \strlen($paymentDTO->sessionId) > 20 ? 
												\substr($paymentDTO->sessionId, 0, 20) : $paymentDTO->sessionId;
		$newBalance = "0";
		$receiptingParams = [ 
										'description' => "Hire of Vacuum Tanker",
										'referenceNumber' => $mulongaTransactionRef,
										'account' => $paymentDTO->customerAccount,
										'amount' => $paymentDTO->receiptAmount,
										'paymentType'=>"999",
										'receiptType'=>"05",
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'client_id' => $paymentDTO->client_id
									];

		return $this->handleCommon($paymentDTO,$newBalance,$receiptingParams);

	}

}