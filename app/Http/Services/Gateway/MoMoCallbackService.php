<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Payments\PaymentToReviewService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use App\Http\Services\Enums\PaymentTypeEnum;
use App\Http\DTOs\MoMoDTO;


use Exception;

class MoMoCallbackService
{

   public function __construct(
      private PaymentToReviewService $paymentToReviewService, 
      private ClientMenuService $clientMenuService,
      private ConfirmPayment $confirmPayment,
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
         $paymentDTO->ppTransactionId = $callbackParams['airtel_money_id'];
         $paymentDTO->callbackResponse = "YES";

         // Handle payment status based on callback status code
         if ($callbackParams['status_code'] === 'TS') {
            $theMenu = $this->clientMenuService->findById($paymentDTO->menu_id);
            if ($theMenu->paymentType === PaymentTypeEnum::PrePaid->value) {
                  $paymentDTO->paymentStatus = PaymentStatusEnum::NoToken->value;
            } else {
                  $paymentDTO->paymentStatus = PaymentStatusEnum::Paid->value;
            }
            $paymentDTO->error = "";
         } else {
            $paymentDTO->paymentStatus = PaymentStatusEnum::Payment_Failed->value;
            $paymentDTO->error = $callbackParams['message'];
         }

         // Confirm the payment through the pipeline
         $paymentDTO = $this->confirmPayment->handle($paymentDTO);

      } catch (\Throwable $e) {
         throw new Exception('Error processing Airtel callback: ' . $e->getMessage());
      }

      return "Handled";
   }
   

}
