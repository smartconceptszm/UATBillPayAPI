<?php

namespace App\Http\Services\MoMo\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;

class Step_UpdateTransaction extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentService $paymentService)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         $this->paymentService->update($momoDTO->toPaymentData(),$momoDTO->id);
      } catch (\Throwable $e) {
         $momoDTO->error='At updating payment record. '.$e->getMessage();
      }
      return $momoDTO;

   }

}