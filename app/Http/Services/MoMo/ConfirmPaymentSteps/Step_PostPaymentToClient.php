<?php

namespace App\Http\Services\MoMo\ConfirmPaymentSteps;

use App\Http\Services\MoMo\BillingClientCallers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private IReceiptPayment $receiptPayment)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {
      try {
         if($momoDTO->error == ''){
            $momoDTO = $this->receiptPayment->handle($momoDTO);
         }
      } catch (Exception $e) {
         $momoDTO->error='At post payment pipeline. '.$e->getMessage();
      }
      return  $momoDTO;
      
   }

}