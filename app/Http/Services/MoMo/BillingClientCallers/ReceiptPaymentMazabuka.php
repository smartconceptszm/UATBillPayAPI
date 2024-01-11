<?php

namespace App\Http\Services\MoMo\BillingClientCallers;

use App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\ClientMenuService;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;


class ReceiptPaymentMazabuka implements IReceiptPayment
{

    public function __construct(
        private IBillingClient $billingClient,
		  private ClientMenuService $clientMenuService)
    {}

    public function handle(BaseDTO $momoDTO): BaseDTO
    {

			$receiptingParams = [ 
										'account' => $momoDTO->accountNumber,
										'amount' => $momoDTO->receiptAmount,
										'mobileNumber'=> $momoDTO->mobileNumber,
										'referenceNumber' => $momoDTO->reference,
									];
			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){
					$momoDTO->receiptNumber = $billingResponse['receiptNumber'];
					$momoDTO->paymentStatus = "RECEIPTED";

					$theMenu = $this->clientMenuService->findById($momoDTO->menu_id);

					$receipt = "Payment successful\n" .
					"Rcpt No.: " . $momoDTO->receiptNumber . "\n" .
					"Amount: ZMW " . \number_format($momoDTO->receiptAmount, 2, '.', ',') . "\n";
					if($momoDTO->accountNumber){
						$receipt .= "Acc: " . $momoDTO->accountNumber . "\n";
					}
					if($momoDTO->reference){
						$receipt .= "Ref: " . $momoDTO->reference . "\n";
					}
					$receipt .= "For: " . $theMenu->description. "\n";
					$receipt .= "Date: " . Carbon::now()->format('d-M-Y') . "\n";
					$momoDTO->receipt = $receipt;
			}else{
					$momoDTO->error = "At post recoonection fee. ".$billingResponse['error'];
			}
        
        return $momoDTO;

    }

}