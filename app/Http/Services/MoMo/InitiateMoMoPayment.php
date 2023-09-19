<?php

namespace App\Http\Services\MoMo;

use App\Http\DTOs\MoMoDTO;
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
            \App\Http\Services\MoMo\InitiatePaymentSteps\Step_GetPaymentAmounts::class,
            \App\Http\Services\MoMo\InitiatePaymentSteps\Step_CreatePaymentRecord::class,
            \App\Http\Services\MoMo\InitiatePaymentSteps\Step_SendMoMoRequest::class, 
            \App\Http\Services\MoMo\InitiatePaymentSteps\Step_DispatchConfirmationJob::class,
            \App\Http\Services\MoMo\Utility\Step_UpdateTransaction::class,  
            \App\Http\Services\MoMo\Utility\Step_LogStatus::class 
         ]
      )
      ->thenReturn();
      return $momoDTO;
   }

}
