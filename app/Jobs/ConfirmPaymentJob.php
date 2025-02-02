<?php

namespace App\Jobs;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ConfirmPaymentJob extends BaseJob
{

   // public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(PaymentsProviderCredentialService $paymentsProviderCredentialService,
                                                                        ConfirmPayment $confirmPayment)
   {

      //SMS handling
         $paymentsProviderCredentials = $paymentsProviderCredentialService->getProviderCredentials($this->paymentDTO->payments_provider_id);
         if($paymentsProviderCredentials[$this->paymentDTO->walletHandler.'_HAS_FREESMS'] == "YES"){
            Cache::put($this->paymentDTO->transactionId.'_smsClient',
                           $this->paymentDTO->walletHandler.'DeliverySMS',Carbon::now()->addSeconds(2));
         }
      //
      //Handle Job Service
         return $confirmPayment->handle($this->paymentDTO);
      //

   }

}