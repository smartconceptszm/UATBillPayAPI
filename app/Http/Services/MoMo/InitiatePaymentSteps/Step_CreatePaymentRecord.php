<?php

namespace App\Http\Services\MoMo\InitiatePaymentSteps;

use App\Http\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;

class Step_CreatePaymentRecord extends EfectivoPipelineContract
{

   public function __construct(
      private OtherPaymentTypeService $otherPayTypes,
      private PaymentService $paymentService)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if($momoDTO->error == ""){
            if($momoDTO->menu == 'OtherPayments'){
               $arrCustomerJourney = \explode("*", $momoDTO->customerJourney);
               $paymentType = $this->otherPayTypes->findOneBy([
                                       'client_id' => $momoDTO->client_id,
                                       'order' => $arrCustomerJourney[2]
                                 ]);
               $momoDTO->other_payment_type_id = $paymentType->id;
               $momoDTO->reference = $arrCustomerJourney[4];
            }
            $payment = $this->paymentService->create($momoDTO->toPaymentData());
            $momoDTO->id = $payment->status;
            $momoDTO->id = $payment->id;
         }
      } catch (\Throwable $e) {
         $momoDTO->error = 'At creating payment record. '.$e->getMessage();
      }
      return $momoDTO;

   }
   
}