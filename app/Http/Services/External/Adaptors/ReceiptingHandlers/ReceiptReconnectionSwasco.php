<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\Swasco;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptReconnectionSwasco implements IReceiptPayment
{

    public function __construct(
        private Swasco $billingClient)
    {}

    public function handle(BaseDTO $paymentDTO): BaseDTO
    {

			$swascoTransactionRef = \strlen($paymentDTO->sessionId) > 20 ? 
													\substr($paymentDTO->sessionId, 0, 20) : $paymentDTO->sessionId;

			$receiptingParams = [ 
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'referenceNumber' => $swascoTransactionRef,
										'account' => $paymentDTO->accountNumber,
										'amount' => $paymentDTO->receiptAmount,
										'client_id' => $paymentDTO->client_id,
										'paymentType'=>"4",
									];
			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){
				$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
				$paymentDTO->paymentStatus = "RECEIPTED";

				$paymentDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->accountNumber . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->error = "At post recoonection fee. ".$billingResponse['error'];
			}
        
        return $paymentDTO;

    }

}