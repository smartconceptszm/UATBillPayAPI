<?php

namespace App\Jobs;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Clients\SMSProviderService;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmPaymentJob extends BaseJob
{

   public $timeout = 180;
   
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(ConfirmPayment $confirmPayment, SMSProviderService $smsProviderService,
                              PaymentsProviderCredentialService $paymentsProviderCredentialService)
   {

      //SMS handling
         $paymentsProviderCredentials = $paymentsProviderCredentialService->getProviderCredentials($this->paymentDTO->payments_provider_id);
         if($paymentsProviderCredentials[$this->paymentDTO->walletHandler.'_HAS_FREESMS'] == "YES"){
            $smsProvider = $smsProviderService->findOneBy(['payments_provider_id' =>$this->paymentDTO->payments_provider_id]);
            $this->paymentDTO->smsHandler = $smsProvider->handler;
         }
      //
      //Handle Job Service
         return $confirmPayment->handle($this->paymentDTO);
      //

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
   */
   public function failed(\Throwable $exception)
   {
      Log::error($exception->getMessage());
   }

}