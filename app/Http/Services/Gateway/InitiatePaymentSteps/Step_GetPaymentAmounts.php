<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\Gateway\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;

class Step_GetPaymentAmounts extends EfectivoPipelineContract
{

   public function __construct(
      private StepService_CalculatePaymentAmounts $calculatePaymentAmounts)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         if( empty($paymentDTO->error)){
            $this->calculatePaymentAmounts->handle($paymentDTO);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At Calculate Payment Amounts. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}