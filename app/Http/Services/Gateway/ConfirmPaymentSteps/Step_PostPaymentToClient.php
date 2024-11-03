<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\External\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private IReceiptPayment $receiptPayment)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         if(($paymentDTO->paymentStatus == 'PAID | NOT RECEIPTED') || ($paymentDTO->paymentStatus == 'PAID | NO TOKEN')){
            $paymentDTO = $this->receiptPayment->handle($paymentDTO);
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}