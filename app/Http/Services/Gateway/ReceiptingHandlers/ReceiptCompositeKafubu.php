<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Payments\CompositeReceiptService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\BaseDTO;

class ReceiptCompositeKafubu implements IReceiptPayment
{

    public function __construct( 
		private CompositeReceiptService $compositeReceiptService,
      private IBillingClient $billingClient
		)
    {}

    public function handle(BaseDTO $receiptDTO):BaseDTO
    {
        
		$receiptingParams = [ 
									'reference' => $receiptDTO->ppTransactionId,
									'providerName'=>$receiptDTO->walletHandler,
									'account' => $receiptDTO->customerAccount,
									'amount' => $receiptDTO->receiptAmount,
									'client_id'=>$receiptDTO->client_id,
									'balance' => $receiptDTO->balance
							];

		$billingResponse=$this->billingClient->postPayment($receiptingParams);

		if($billingResponse['status']=='SUCCESS'){
			$receiptDTO->receiptNumber = $billingResponse['receiptNumber'];
			$receiptDTO->status =  PaymentStatusEnum::Receipted->value;
		}else{
			$receiptDTO->error = "At receipt composite payment. ".$billingResponse['error'];
		}

		//Create receipt record locally
		if($receiptDTO->id){
			$this->compositeReceiptService->update(['receiptNumber'=>$receiptDTO->receiptNumber,
																	'status'=>$receiptDTO->status,
																	],
																	$receiptDTO->id
																);
		}else{
			$this->compositeReceiptService->create($receiptDTO->toReceiptData());
		}
		
		return $receiptDTO;
		
    }

}