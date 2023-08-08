<?php

namespace App\Http\BillPay\Services\MoMo\InitiatePaymentSteps;

use App\Http\BillPay\Services\MenuConfigs\OtherPaymentTypeService;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineContract;
use App\Http\BillPay\Services\Payments\PaymentService;
use App\Http\BillPay\DTOs\BaseDTO;

class Step_CreatePaymentRecord extends EfectivoPipelineContract
{

   private  $paymentService;
   private $otherPayTypes;
   public function __construct(PaymentService $paymentService,
      OtherPaymentTypeService $otherPayTypes)
   {
      $this->paymentService= $paymentService;
      $this->otherPayTypes = $otherPayTypes;
   }

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