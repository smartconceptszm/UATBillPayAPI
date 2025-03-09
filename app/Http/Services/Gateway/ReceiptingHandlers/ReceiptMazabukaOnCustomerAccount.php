<?php

namespace App\Http\Services\Gateway\ReceiptingHandlers;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientRevenueCodeService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptMazabukaOnCustomerAccount implements IReceiptPayment
{

    public function __construct(
			private ClientRevenueCodeService $revenueCodeService,
		  	private ClientMenuService $clientMenuService,
        	private IBillingClient $billingClient)
    {}

    public function handle(BaseDTO $paymentDTO): BaseDTO
    {
			$receiptingParams = [ 
										'customerAccount' => $paymentDTO->customerAccount,
										'receiptAmount' => $paymentDTO->receiptAmount,
										'client_id' => $paymentDTO->client_id,
										'payment_id' => $paymentDTO->id
									];

			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){

				$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
				$paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;

				$theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
				$parentMenu = $this->clientMenuService->findById($theMenu->parent_id);
				$paymentDTO->receipt = "\n"."Payment successful"."\n".
								"Rcpt No.: " . $paymentDTO->receiptNumber . "\n" .
								"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
								"Acc: " . $paymentDTO->customerAccount . "\n".
								"For: (".$theMenu->commonAccount.") - ".$parentMenu->prompt.": ".$theMenu->prompt."\n".
								"Ref: ".$paymentDTO->reference."\n".
								"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
				$paymentDTO->error = "At receipt Council payment. ".$billingResponse['error'];
			}
        
        return $paymentDTO;

    }

}