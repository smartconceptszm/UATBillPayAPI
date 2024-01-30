<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\Services\MoMo\Utility\StepService_AddShotcutMessage;
use App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;


class ReceiptPaymentChambeshi implements IReceiptPayment
{

	public function __construct(
		private StepService_AddShotcutMessage $addShotcutMessage,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		$customer = \json_decode(Cache::get($momoDTO->urlPrefix.
										$momoDTO->accountNumber,\json_encode([])), true);
		$transactDescript = "Post-Paid Bill payment via 2220";
		if($momoDTO->receiptNumber){
			$transactDescript = "Pre-Paid Bill payment via 2220";
		}else{
			$momoDTO->receiptNumber =  $momoDTO->accountNumber."_".date('YmdHis');
		}
		
		$receiptingParams=[
            "TxDate"=> $momoDTO->created_at,
            "Account"=> $momoDTO->accountNumber,  
            "AccountName"=> $customer['name'],
            "Debt"=> 0,
            "AmountPaid" => $momoDTO->receiptAmount, 
            "Phone#"=> $momoDTO->mobileNumber,
            "ReceiptNo"=> $momoDTO->receiptNumber,
            "Address"=> $momoDTO->accountNumber,
            "District"=> $momoDTO->district,
            "TransactDescript"=> $transactDescript,
            "Mobile Network" => $momoDTO->mnoName, 
            
       ];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		
		if($billingResponse['status']=='SUCCESS'){
				$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
				$momoDTO->paymentStatus = "RECEIPTED";

				$momoDTO->receipt = "Payment successful\n" .
				"Receipt No .: " . $momoDTO->receiptNumber . "\n" .
				"Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
				"Acc: " . $momoDTO->accountNumber . "\n";
				//$momoDTO->receipt.="Bal: ZMW ". $newBalance . "\n";
				$momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";

				// if ((($momoDTO->mobileNumber == $customer['mobileNumber']) && ($momoDTO->channel == 'USSD'))){
				// 	try {
				// 		$momoDTO = $this->addShotcutMessage->handle($momoDTO);
				// 	} catch (Exception $e) {
				// 		Log::error('('.$momoDTO->clientCode.'). '.$e->getMessage().
				// 			'- Session: '.$momoDTO['sessionId'].' - Phone: '.$momoDTO->mobileNumber);
				// 	}
				// }

		}else{
				$momoDTO->error = "At post payment. ".$billingResponse['error'];
		}
		return $momoDTO;

	}

}