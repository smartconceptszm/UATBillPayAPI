<?php

namespace App\Http\Services\External\ReceiptingHandlers;

use App\Http\Services\External\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptReconnectionSwasco implements IReceiptPayment
{

    public function __construct(
        private IBillingClient $billingClient)
    {}

    public function handle(BaseDTO $paymentDTO): BaseDTO
    {

			$swascoTransactionRef = \strlen($paymentDTO->sessionId) > 20 ? 
													\substr($paymentDTO->sessionId, 0, 20) : $paymentDTO->sessionId;

			$receiptingParams = [ 
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'referenceNumber' => $swascoTransactionRef,
										'account' => $paymentDTO->customerAccount,
										'created_at' => $paymentDTO->created_at,
										'amount' => $paymentDTO->receiptAmount,
										'client_id' => $paymentDTO->client_id,
										'paymentType'=>"4",
									];
			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){
				$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
				$paymentDTO->paymentStatus = "RECEIPTED";

				$paymentDTO->receipt = "\n"."Payment successful"."\n".
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->customerAccount . "\n".
											"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->error = "At post recoonection fee. ".$billingResponse['error'];
			}
        
        return $paymentDTO;

    }

}