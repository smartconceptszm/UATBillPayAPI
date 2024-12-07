<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Gateway\ReceiptingHandlers\IReceiptPayment;
use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Payments\PaymentService;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\DTOs\BaseDTO;

class Step_PostPaymentToClient extends EfectivoPipelineContract
{

   public function __construct(
      private PaymentService $paymentService,
      private IReceiptPayment $receiptPayment)
   {}

   protected function stepProcess(BaseDTO $paymentDTO)
   {
      
      try {

         if(   ($paymentDTO->paymentStatus == PaymentStatusEnum::Paid->value) || 
               ($paymentDTO->paymentStatus == PaymentStatusEnum::NoToken->value)
         ){
            
            if($paymentDTO->receiptNumber  != ''){
               $paymentDTO->paymentStatus = PaymentStatusEnum::Receipted->value;
            }else{
               $paymentDTO = $this->receiptPayment->handle($paymentDTO);
            }
            
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At post payment step. '.$e->getMessage();
      }
      return  $paymentDTO;
      
   }

}