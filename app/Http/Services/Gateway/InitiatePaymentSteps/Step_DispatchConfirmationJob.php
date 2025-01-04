<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Clients\ClientWalletCredentialsService;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Clients\ClientWalletService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Jobs\ReConfirmCallBackPaymentJob;
use App\Jobs\ReConfirmPaymentJob;
use App\Jobs\ConfirmPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DispatchConfirmationJob extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private ClientWalletCredentialsService $walletCredentialsService,
      private ClientWalletService $clientWalletService) 
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         $walletCredentials = $this->walletCredentialsService->getWalletCredentials($paymentDTO->wallet_id);
         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);
         if($walletCredentials['CALLBACK_ENABLED'] != 'YES'){
            if( $paymentDTO->paymentStatus == PaymentStatusEnum::Submitted->value){
               $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
               $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);
               ConfirmPaymentJob::dispatch($paymentDTO)
                              ->delay(Carbon::now()->addSeconds(
                                       (int)$paymentsProviderCredentials[$paymentDTO->walletHandler.'_PAYSTATUS_CHECK'])
                                    )
                              ->onQueue('high');
            }else{
               ReConfirmPaymentJob::dispatch($paymentDTO)
                                 ->delay(Carbon::now()->addMinutes((int)$billpaySettings['PAYMENT_REVIEW_DELAY']))
                                 ->onQueue('high');
            }
         }else{
            ReConfirmCallBackPaymentJob::dispatch($paymentDTO->id)
                                 ->delay(Carbon::now()->addMinutes((int)$billpaySettings['PAYMENT_REVIEW_DELAY']))
                                 ->onQueue('high');
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At dispatching confirmation job. '.$e->getMessage();
      }
      return $paymentDTO;

   }
}