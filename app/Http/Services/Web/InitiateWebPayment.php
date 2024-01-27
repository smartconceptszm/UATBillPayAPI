<?php

namespace App\Http\Services\MoMo;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class InitiateWebPayment
{

   public function handle(BaseDTO $momoDTO)
   {
      
      //Process the request
      try {
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
      } catch (\Throwable $e) {
         $momoDTO->error='At get initiate web payment pipeline. '.$e->getMessage();
         Log::info($momoDTO->error);
      }

      return $momoDTO;
      
   }

}
