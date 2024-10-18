<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientWalletCredentialsService;
use App\Http\Services\Clients\ClientWalletService;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmPaymentJob;
use App\Jobs\ConfirmPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DispatchConfirmationJob extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private ClientWalletCredentialsService $clientWalletCredentialsService,
      private ClientWalletService $clientWalletService) 
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if( $paymentDTO->paymentStatus == "SUBMITTED"){

            $walletCredentials = $this->clientWalletCredentialsService->getWalletCredentials($paymentDTO->wallet_id);
            if($walletCredentials['CALLBACK_ENABLED'] == 'NO'){
               $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
               $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);
               
               Queue::later(Carbon::now()->addSeconds(
                  (int)$paymentsProviderCredentials[$paymentDTO->walletHandler.'_PAYSTATUS_CHECK']),
                                       new ConfirmPaymentJob($paymentDTO),'','high');
            }                      
         }else{
            $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
            Queue::later(Carbon::now()->addMinutes((int)$billpaySettings['PAYMENT_REVIEW_DELAY']),
                           new ReConfirmPaymentJob($paymentDTO),'','high');
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At dispatching confirmation job. '.$e->getMessage();
      }
      return $paymentDTO;

   }
}