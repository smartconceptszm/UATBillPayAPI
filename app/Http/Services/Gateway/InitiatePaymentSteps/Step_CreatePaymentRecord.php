<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_CreatePaymentRecord extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentService $paymentService)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if($paymentDTO->error == ""){
            $payment = $this->paymentService->create($paymentDTO->toPaymentData());
            $paymentDTO->created_at = $payment->created_at->toDateTimeString();
            $paymentDTO->status = $payment->status;
            $paymentDTO->id = $payment->id;
         }
      } catch (\Throwable $e) {
         $paymentDTO->error = 'At creating payment record. '.$e->getMessage();
      }
      return $paymentDTO;

   }
   
}