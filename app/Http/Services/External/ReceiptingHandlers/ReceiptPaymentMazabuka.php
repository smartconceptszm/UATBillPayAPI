<?php

namespace App\Http\Services\External\ReceiptingHandlers;

use App\Http\Services\External\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\External\BillingClients\IBillingClient;
use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Clients\ClientMenuService;

use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class ReceiptPaymentMazabuka implements IReceiptPayment
{

    public function __construct(
			private BillingCredentialService $billingCredentials,
		  	private ClientMenuService $clientMenuService,
        	private IBillingClient $billingClient)
    {}

    public function handle(BaseDTO $paymentDTO): BaseDTO
    {

			$receiptingParams = [ 
										'account' => $paymentDTO->customerAccount,
										'amount' => $paymentDTO->receiptAmount,
										'mobileNumber'=> $paymentDTO->mobileNumber,
										'client_id' => $paymentDTO->client_id,
										'referenceNumber' => $paymentDTO->reference,
									];
			$billingResponse = $this->billingClient->postPayment($receiptingParams);

			if($billingResponse['status']=='SUCCESS'){

				$customerJourney = \explode("*", $paymentDTO->customerJourney);
				$billingCredential = $this->billingCredentials->findOneBy(['client_id' =>$paymentDTO->client_id,
																									'key' =>$customerJourney[2]]);
					$paymentDTO->receiptNumber = $billingResponse['receiptNumber'];
					$paymentDTO->paymentStatus = "RECEIPTED";

					$theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
					$paymentDTO->receipt = "\n"."Payment successful"."\n".
									"Rcpt No.: " . $paymentDTO->receiptNumber . "\n" .
									"Amount: ZMW " . \number_format($paymentDTO->receiptAmount, 2, '.', ',') . "\n".
									"For: (".$billingCredential->key.") - ".$billingCredential->keyValue."\n".
									"Name: ".$paymentDTO->reference."\n".
									"Date: " . Carbon::now()->format('d-M-Y') . "\n";
									
			}else{
				$paymentDTO->error = "At Council payment. ".$billingResponse['error'];
			}
        
        return $paymentDTO;

    }

}