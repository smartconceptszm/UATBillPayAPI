<?php

namespace App\Http\Services\MoMo\InitiatePaymentSteps;

use App\Http\Services\MoMo\Utility\StepService_CalculatePaymentAmounts;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_GetPaymentAmounts extends EfectivoPipelineContract
{

   public function __construct(
      private StepService_CalculatePaymentAmounts $calculatePaymentAmounts)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {
      
      try {
         if($momoDTO->error==""){
            $calculatedAmounts = $this->calculatePaymentAmounts->handle(
                                       $momoDTO->urlPrefix,$momoDTO->mno_id,$momoDTO->paymentAmount);
            $momoDTO->surchargeAmount = $calculatedAmounts['surchargeAmount'];
            $momoDTO->receiptAmount = $calculatedAmounts['receiptAmount'];
            $momoDTO->paymentAmount = $calculatedAmounts['paymentAmount'];
            $momoDTO->clientCode = $calculatedAmounts['clientCode'];
         }
      } catch (Exception $e) {
         $momoDTO->error='At Calculate Payment Amounts. '.$e->getMessage();
      }
      return $momoDTO;

   }

}