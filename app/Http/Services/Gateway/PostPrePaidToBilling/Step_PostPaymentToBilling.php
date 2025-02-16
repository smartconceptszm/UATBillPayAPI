<?php

namespace App\Http\Services\Gateway\PostPrePaidToBilling;

use App\Http\Services\Gateway\ReceiptingHandlers\ReceiptPrePaidChambeshi;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToBilling extends EfectivoPipelineContract
{

   public function __construct(
      private ReceiptPrePaidChambeshi $receiptPrePaidChambeshi)
   {}    

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {
         $paymentDTO = $this->receiptPrePaidChambeshi->handle($paymentDTO);
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment to billing. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}