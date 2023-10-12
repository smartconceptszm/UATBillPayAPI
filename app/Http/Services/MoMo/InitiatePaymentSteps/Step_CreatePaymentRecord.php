<?php

namespace App\Http\Services\MoMo\InitiatePaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_CreatePaymentRecord extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentService $paymentService)
   {}

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if($momoDTO->error == ""){
            $payment = $this->paymentService->create($momoDTO->toPaymentData());
            $momoDTO->id = $payment->status;
            $momoDTO->id = $payment->id;
         }
      } catch (Exception $e) {
         if(\substr($e->getMessage(),0,8) == "SQLSTATE"){
            $momoDTO->error = 'Duplicate initiate payment job. Session_id = '.$momoDTO->session_id.". Details: ".$e->getMessage();
         }else{
            $momoDTO->error = 'At creating payment record. '.$e->getMessage();
         }
      }
      return $momoDTO;

   }
   
}