<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class ReceiptReconnectionFeesSwasco implements IReceiptPayment
{

    public function __construct(
        private IBillingClient $billingClient)
    {}

    public function handle(BaseDTO $momoDTO): BaseDTO
    {
			$paymentType = "4";
			$swascoTransactionRef = \strlen($momoDTO->sessionId) > 20 ? 
													\substr($momoDTO->sessionId, 0, 20) : $momoDTO->sessionId;
			$receiptingParams = [ 
											'paymentType'=>$paymentType,
											'account' => $momoDTO->accountNumber,
											'amount' => $momoDTO->receiptAmount,
											'mobileNumber"'=> $momoDTO->mobileNumber,
											'referenceNumber' => $swascoTransactionRef,
									];
			$billingResponse=$this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){
					$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
					$momoDTO->paymentStatus = "RECEIPTED";

					$receipt = "Payment successful\n" .
					"Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
					"Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
					"Acc: " . $momoDTO->accountNumber . "\n";
					$receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";

					$momoDTO->receipt = $receipt;
			}else{
					$momoDTO->error = "At post recoonection fee. ".$billingResponse['error'];
			}
        
        return $momoDTO;

    }

}