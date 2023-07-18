<?php

namespace App\Http\BillPay\Services\MoMo;

use App\Http\BillPay\DTOs\MoMoDTO;
use Illuminate\Pipeline\Pipeline;

class InitiateMoMoPayment
{

   public function handle(MoMoDTO $momoDTO)
   {
      
      //Process the request
      $momoDTO  =  app(Pipeline::class)
      ->send($momoDTO)
      ->through(
         [
            \App\Http\BillPay\Services\MoMo\InitiatePaymentSteps\Step_GetPaymentAmounts::class,
            \App\Http\BillPay\Services\MoMo\InitiatePaymentSteps\Step_CreatePaymentRecord::class,
            \App\Http\BillPay\Services\MoMo\InitiatePaymentSteps\Step_SendMoMoRequest::class, 
            \App\Http\BillPay\Services\MoMo\InitiatePaymentSteps\Step_DispatchConfirmationJob::class,
            \App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction::class,  
            \App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus::class 
         ]
      )
      ->thenReturn();
      return $momoDTO;
   }

}
