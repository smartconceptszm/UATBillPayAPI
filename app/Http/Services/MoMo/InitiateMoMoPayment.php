<?php

namespace App\Http\Services\MoMo;

use Illuminate\Support\Facades\App;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\MoMoDTO;

class InitiateMoMoPayment
{

   public function handle(MoMoDTO $momoDTO)
   {
      
      //Process the request
      $momoDTO  =  App::make(Pipeline::class)
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
