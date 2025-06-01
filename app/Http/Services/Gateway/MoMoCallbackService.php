<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Gateway\Utility\StepService_ProcessPromotion;
use App\Http\Services\Clients\ClientWalletCredentialsService;
use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ReConfirmPayment;
use App\Http\Services\Enums\PaymentTypeEnum;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\MoMoDTO;

use Exception;

class MoMoCallbackService
{

   public function __construct(
      private ClientWalletCredentialsService $clientWalletCredentialsService,
      private StepService_ProcessPromotion $stepServiceProcessPromotion,
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private ReConfirmPayment $reConfirmPayment,
      private MoMoDTO $paymentDTO)
   {}

   public function handleAirtel(array $callbackParams): string
   {

      try {


         // Validate required callback parameters
         if (!isset($callbackParams['id'], $callbackParams['status_code'], $callbackParams['airtel_money_id'])) {
            throw new Exception('Missing required parameters in callback');
         }

         // Fetch payment by transaction ID
         $thePayment = $this->paymentToReviewService->findByTransactionId($callbackParams['id']);
         if (!$thePayment) {
            throw new Exception('Payment not found for transaction ID: ' . $callbackParams['id']);
         }

         // Create payment DTO from payment data
         $paymentDTO = $this->paymentDTO->fromArray(\get_object_vars($thePayment));
         $walletCredentials = $this->clientWalletCredentialsService->getWalletCredentials($paymentDTO->wallet_id);

         if ($walletCredentials['CALLBACK_ENABLED'] == 'YES') {
            $paymentDTO->ppTransactionId = $callbackParams['airtel_money_id'];
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            // Handle payment status based on callback status code
            if ($callbackParams['status_code'] === 'TS') {
               if ($theMenu->paymentType === PaymentTypeEnum::PrePaid->value) {
                  $paymentDTO->paymentStatus = PaymentStatusEnum::NoToken->value;
               } else {
                  $paymentDTO->paymentStatus = PaymentStatusEnum::Paid->value;
               }
               $paymentDTO->error = "";

               //Fire Promotion
               $this->stepServiceProcessPromotion->handle($paymentDTO);

            } else {
               $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
               $paymentDTO->error = $callbackParams['message'];
            }
            //Log the Callback Execution
               $logMessage = '('.$paymentDTO->urlPrefix.') Callback executed  on ('.strtoupper($paymentDTO->walletHandler).
                                 ') for Session: '.$paymentDTO->sessionId.'. Transaction ID = '.$paymentDTO->transactionId.
                                 '. Channel:'.$paymentDTO->channel.'. Wallet: '.$paymentDTO->walletNumber.'. Payment Status:'.
                                 $paymentDTO->paymentStatus;
               Log::info($logMessage);
               
               
            // Confirm the payment through the pipeline
            $paymentDTO = $this->reConfirmPayment->handle($paymentDTO);
         }

      } catch (\Throwable $e) {
         throw new Exception('Error processing Airtel callback: ' . $e->getMessage());
      }

      return "Handled";
   }
   

}