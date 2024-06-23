<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\LukangaPostPaid;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidLukanga implements IReceiptPayment
{

    public function __construct( 
        private LukangaPostPaid $billingClient)
    {}

    public function handle(BaseDTO $paymentDTO):BaseDTO
    {
        
		$newBalance="0";
		if($paymentDTO->customer){
			if(\key_exists('balance',$paymentDTO->customer)){
				$newBalance = (float)(\str_replace(",", "", $paymentDTO->customer['balance'])) - 
												(float)$paymentDTO->receiptAmount;
				$newBalance = \number_format($newBalance, 2, '.', ',');
			}
		}

		$receiptingParams=[ 
				'account' => $paymentDTO->accountNumber,
				'reference' => $paymentDTO->ppTransactionId,
				'amount' => $paymentDTO->receiptAmount,
				'mnoName'=>$paymentDTO->walletHandler,
				'client_id' => $paymentDTO->client_id,
				'balance' => $paymentDTO->customer?(float)(\str_replace(",", "", $paymentDTO->customer['balance'])):0
		];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status']=='SUCCESS'){
				$paymentDTO->receiptNumber=$billingResponse['receiptNumber'];
				$paymentDTO->paymentStatus = 'RECEIPTED';
				$paymentDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $paymentDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $paymentDTO->accountNumber . "\n";
				if($newBalance!=="0"){
					$paymentDTO->receipt.="Bal: ZMW ".$newBalance . "\n";
				}
				$paymentDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
				$paymentDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $paymentDTO;
		
    }

}