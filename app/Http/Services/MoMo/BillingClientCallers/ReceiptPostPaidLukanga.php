<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPostPaidLukanga implements IReceiptPayment
{
    private $newBalance;
    public function __construct( 
        private IBillingClient $billingClient)
    {}

    public function handle(BaseDTO $momoDTO):BaseDTO
    {
        
		$this->newBalance="0";
		if($momoDTO->customer){
			if(\key_exists('balance',$momoDTO->customer)){
				$this->newBalance = (float)(\str_replace(",", "", $momoDTO->customer['balance'])) - 
												(float)$momoDTO->receiptAmount;
				$this->newBalance = \number_format($this->newBalance, 2, '.', ',');
			}else{
				$this->newBalance = "0";
			}
		}

		$receiptingParams=[ 
				'account' => $momoDTO->accountNumber,
				'reference' => $momoDTO->mnoTransactionId,
				'amount' => $momoDTO->receiptAmount,
				'mnoName'=>$momoDTO->mnoName,
				'balance' => $momoDTO->customer?(float)(\str_replace(",", "", $momoDTO->customer['balance'])):0
		];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		if($billingResponse['status']=='SUCCESS'){
				$momoDTO->receiptNumber=$billingResponse['receiptNumber'];
				$momoDTO->paymentStatus = 'RECEIPTED';
				$momoDTO->receipt = "Payment successful\n" .
											"Rcpt No: " . $momoDTO->receiptNumber . "\n" .
											"Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n".
											"Acc: " . $momoDTO->accountNumber . "\n";
											if($this->newBalance!="0"){
												$momoDTO->receipt.="Bal: ZMW ".$this->newBalance . "\n";
											}
				$momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";
		}else{
				$momoDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $momoDTO;
		
    }

}