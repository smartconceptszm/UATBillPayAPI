<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Web\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;

class Step_UpdateTransaction extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentService $paymentService)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if($paymentDTO->id){
            $this->paymentService->update($paymentDTO->toPaymentData(),$paymentDTO->id);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At updating payment record. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}