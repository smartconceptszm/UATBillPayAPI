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
   private const PAYMENT_REVIEW_DELAY_KEY = 'PAYMENT_REVIEW_DELAY';

   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private ClientWalletCredentialsService $walletCredentialsService,
      private ClientWalletService $clientWalletService
   ) {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      try {
         $walletCredentials = $this->walletCredentialsService->getWalletCredentials($paymentDTO->wallet_id);
         $billpaySettings = json_decode(cache('billpaySettings', json_encode([])), true);

         if ($walletCredentials['CALLBACK_ENABLED'] == 'YES') {
            $this->dispatchCallBackJob($paymentDTO, $billpaySettings);
         } else {
            $this->dispatchJobBasedOnPaymentStatus($paymentDTO, $billpaySettings);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At dispatching confirmation job. ' . $e->getMessage();
      }

      return $paymentDTO;
   }

   private function dispatchCallBackJob(BaseDTO $paymentDTO, array $billpaySettings)
   {
      $paymentReviewDelay = (int) $billpaySettings[self::PAYMENT_REVIEW_DELAY_KEY];
      ReConfirmCallBackPaymentJob::dispatch($paymentDTO->id)
                                    ->delay(Carbon::now()->addMinutes($paymentReviewDelay))
                                    ->onQueue('high');
   }

   private function dispatchJobBasedOnPaymentStatus(BaseDTO $paymentDTO, array $billpaySettings)
   {

      $paymentReviewDelay = (int) $billpaySettings[self::PAYMENT_REVIEW_DELAY_KEY];

      if ($paymentDTO->paymentStatus === PaymentStatusEnum::Submitted->value) {
         $this->dispatchConfirmPaymentJob($paymentDTO);
      } else {
         $this->dispatchReConfirmPaymentJob($paymentDTO, $paymentReviewDelay);
      }
   }

   private function dispatchConfirmPaymentJob(BaseDTO $paymentDTO)
   {
      $clientWallet = $this->clientWalletService->findById($paymentDTO->wallet_id);
      $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($clientWallet->payments_provider_id);
      $delaySeconds = (int)$paymentsProviderCredentials[$paymentDTO->walletHandler . '_PAYSTATUS_CHECK'];

      ConfirmPaymentJob::dispatch($paymentDTO)
                        ->delay(Carbon::now()->addSeconds($delaySeconds))
                        ->onQueue('high');
   }

   private function dispatchReConfirmPaymentJob(BaseDTO $paymentDTO, int $paymentReviewDelay)
   {
      ReConfirmPaymentJob::dispatch($paymentDTO)
                           ->delay(Carbon::now()->addMinutes($paymentReviewDelay))
                           ->onQueue('high');
   }


}
