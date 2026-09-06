<?php

namespace App\Http\Services\PublicAPI;

use App\Http\Services\Clients\PaymentsProviderCredentialService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\PublicAPI\IInitiateAPIPayment;
use Illuminate\Support\Facades\Log;
use App\Jobs\InitiateAPIPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\MoMoDTO;
use Exception;

class InitiateMoMoAPIPaymentService implements IInitiateAPIPayment
{
   
   public function __construct(
      private PaymentsProviderCredentialService $paymentsProviderCredentialService,
      private PaymentToReviewService $paymentToReviewService,
      private MoMoDTO $momoDTO) 
   {}

   public function handle(array $params): array{

      try {

         $thePayment = $this->paymentToReviewService->findById($params['payment_id']);
         $paymentDTO = $this->momoDTO->fromArray(\get_object_vars($thePayment));
         $paymentsProviderCredentials = $this->paymentsProviderCredentialService->getProviderCredentials($paymentDTO->payments_provider_id);

         InitiateAPIPaymentJob::dispatch($paymentDTO)
                           ->delay(Carbon::now()
                           ->addSeconds((int)$paymentsProviderCredentials[$paymentDTO->walletHandler.'_SUBMIT_PAYMENT']))
                           ->onQueue('high');

         Log::info('('.$paymentDTO->urlPrefix.') '.
                        'MoMo API payment initiated: Wallet: '.
                           $paymentDTO->walletNumber.' - Phone: '.
                           $paymentDTO->mobileNumber.' - Account Number: '.
                           $paymentDTO->customerAccount.' - Amount: '.
                           $paymentDTO->paymentAmount
                        );

      } catch (\Throwable $e) {
         if($e->getCode()==1){
            throw new Exception("Invalid amount enterred.");
         }
         throw new Exception($e->getMessage());
      }

      return [
               'payment_id' => $paymentDTO->id,
               'paymentStatusCheck' => (int) $paymentsProviderCredentials[$paymentDTO->walletHandler.'_PAYSTATUS_CHECK'],
               'message' => \strtoupper($paymentDTO->urlPrefix)." Payment request submitted to ".$paymentDTO->walletHandler.
                                    ". You will receive a PIN prompt shortly!"
            ];

   }

}


