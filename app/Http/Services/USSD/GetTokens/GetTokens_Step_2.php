<?php

namespace App\Http\Services\USSD\GetTokens;

use App\Http\Services\Clients\BillingCredentialService;
use App\Http\Services\Payments\PaymentHistoryService;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetTokens_Step_2
{

   public function __construct( 
		private BillingCredentialService $billingCredentialService,
      private PaymentHistoryService $paymentHistoryService
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
			$txDTO->customerAccount = $txDTO->subscriberInput;
			$billingCredentials = $this->billingCredentialService->getClientCredentials($txDTO->client_id);
			$tokens = $this->paymentHistoryService->getLatestTokens([
									'limit' => $billingCredentials['TOKEN_HISTORY'],
									'customerAccount' => $txDTO->customerAccount,
									'client_id' => $txDTO->client_id,
								]);
			if($tokens){
				$prompt = "Tokens for ".$txDTO->customerAccount.":\n";
				foreach ($tokens as $key=>$token) {
					
					$prompt .= ($key+1).". ".$token->tokenNumber.
									//" ".Carbon::parse($token->created_at)->format('d-M-Y').
									" ZMW ".number_format($token->receiptAmount, 2, '.', ',')."\n";
				}
				$txDTO->response = $prompt;
				$txDTO->lastResponse = true;
			}else{
				$txDTO->response = "There are no Tokens issued to Meter: ".$txDTO->customerAccount;
				$txDTO->lastResponse = true;
			}
			return $txDTO;

      } catch (\Throwable $e) {
			$txDTO->error = 'At get tokens step 2. '.$e->getMessage();
			$txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
   }

}