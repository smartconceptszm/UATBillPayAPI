<?php

namespace App\Http\BillPay\Services\MoMo\InitiatePaymentSteps;

use App\Http\BillPay\Services\MoMo\Utility\StepService_CalculatePaymentAmounts;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class Step_GetPaymentAmounts extends EfectivoPipelineContract
{

   private $calculatePaymentAmounts;
   public function __construct(StepService_CalculatePaymentAmounts $calculatePaymentAmounts)
   {
      $this->calculatePaymentAmounts=$calculatePaymentAmounts;
   }

   protected function stepProcess(BaseDTO $momoDTO)
   {
      
      try {
         if($momoDTO->error==""){
            $arrCustomerJourney = \explode("*", $momoDTO->customerJourney);
            switch ($momoDTO->menu) {
               case 'PayBill':
                  $paymentAmount = \str_replace(",", "",$arrCustomerJourney[3]);
                  break;
               case 'BuyUnits':
                  $paymentAmount = \str_replace(",", "",$arrCustomerJourney[3]);
                  break;
               case 'OtherPayments':
                  $paymentAmount = \str_replace(",", "",$arrCustomerJourney[5]);
                  break;
               default:
                  throw new Exception("Unknown payment type", 1);
                  break;
            }
            $calculatedAmounts = $this->calculatePaymentAmounts->handle(
                                       $momoDTO->urlPrefix,$momoDTO->mno_id,$paymentAmount);
            $momoDTO->surchargeAmount = $calculatedAmounts['surchargeAmount'];
            $momoDTO->receiptAmount = $calculatedAmounts['receiptAmount'];
            $momoDTO->paymentAmount = $calculatedAmounts['paymentAmount'];
            $momoDTO->clientCode = $calculatedAmounts['clientCode'];
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At Calculate Payment Amounts. '.$e->getMessage();
      }
      return $momoDTO;

   }

}