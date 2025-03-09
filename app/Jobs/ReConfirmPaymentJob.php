<?php

namespace App\Jobs;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Gateway\ReConfirmPayment;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ReConfirmPaymentJob extends BaseJob
{

   public $timeout = 180;

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(PaymentsProviderCredentialService $paymentsProviderCredentialService,
                           ClientWalletService $clientWalletService, ReConfirmPayment $reConfirmPayment)
   {

      $clientWallet = $clientWalletService->findById($this->paymentDTO->wallet_id);
      $paymentsProviderCredentials = $paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);
      if($paymentsProviderCredentials['TRANSACTION_CAN_BE_RECONFIRMED'] == 'YES'){
         //Handle Job Service
            Log::info('('.$this->paymentDTO->urlPrefix.') Reconfirmation job launched. Transaction ID = '.$this->paymentDTO->transactionId.
                        '- Channel: '.$this->paymentDTO->channel.' - Wallet: '.$this->paymentDTO->walletNumber);
                        
            $reConfirmPayment->handle($this->paymentDTO);
         //
      }
   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}
    
}