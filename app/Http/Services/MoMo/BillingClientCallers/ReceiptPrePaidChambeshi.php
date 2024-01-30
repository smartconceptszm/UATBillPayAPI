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


class ReceiptPrePaidChambeshi implements IReceiptPayment
{

	public function __construct(
		private StepService_AddShotcutMessage $addShotcutMessage,
		private IBillingClient $billingClient)
	{}

	public function handle(BaseDTO $momoDTO):BaseDTO
	{

		$paymentType = "1";
		$newBalance = "0";
		$customer=null;
	

		$receiptingParams=[
			 "user_name"=> "POSTEST",
			 "password"=> "6nz8rnut@999",
			 "password_vend"=> "123456",
			 "meter_number"=> $momoDTO->accountNumber,
			 "total_paid" => $momoDTO->receiptAmount, 
			 "debt_percent"=> 50
		];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);
		
		if($billingResponse['status']=='SUCCESS'){
				$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
				$momoDTO->paymentStatus = "RECEIPTED";

				$momoDTO->receipt = "Payment successful\n" .
				"Token .: " . $momoDTO->receiptNumber . "\n" .
				"Amount: ZMW " . \number_format( $momoDTO->receiptAmount, 2, '.', ',') . "\n".
				"Acc: " . $momoDTO->accountNumber . "\n";
				// if($newBalance != "0"){
					$momoDTO->receipt.="Bal: ZMW ". $newBalance . "\n";
				// }
				$momoDTO->receipt.="Date: " . Carbon::now()->format('d-M-Y') . "\n";

		//THIS COMMENTED OUT BY KELO, WILL BE ADDED LATER
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