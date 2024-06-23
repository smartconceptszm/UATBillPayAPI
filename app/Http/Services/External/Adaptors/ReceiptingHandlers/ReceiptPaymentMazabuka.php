<?php

namespace App\Http\Services\External\Adaptors\ReceiptingHandlers;

use App\Http\Services\External\Adaptors\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\Mazabuka;
use App\Http\Services\Web\Clients\ClientMenuService;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPaymentMazabuka implements IReceiptPayment
{

    public function __construct(
        private Mazabuka $billingClient,
		  private ClientMenuService $clientMenuService)
    {}

    public function handle(BaseDTO $paymentDTO): BaseDTO
    {

			$receiptingParams = [ 
										'account' => $paymentDTO->accountNumber,
										'amount' => $paymentDTO->receiptAmount,
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'client_id' => $paymentDTO->client_id,
										'referenceNumber' => $paymentDTO->reference,
									];
			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){
					$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
					$paymentDTO->paymentStatus = "RECEIPTED";

					$theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
					$theMenu = \is_null($theMenu)?null: (object)$theMenu->toArray();
					$paymentDTO->receipt = "Payment successful\n" .
									"Rcpt No.: " . $paymentDTO->receiptNumber . "\n" .
									"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n";
					if($paymentDTO->accountNumber){
						$paymentDTO->receipt .= "Acc: " . $paymentDTO->accountNumber . "\n";
					}
					if($paymentDTO->reference){
						$paymentDTO->receipt .= "Ref: " . $paymentDTO->reference . "\n";
					}
					$paymentDTO->receipt .= "For: " . $theMenu->description. "\n".
												"Date: " . Carbon::now()->format('d-M-Y') . "\n";
			}else{
					$paymentDTO->error = "At post recoonection fee. ".$billingResponse['error'];
			}
        
        return $paymentDTO;

    }

}