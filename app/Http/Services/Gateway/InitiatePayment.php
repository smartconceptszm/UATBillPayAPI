<?php

namespace App\Http\Services\Gateway;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class InitiatePayment
{

   public function handle(BaseDTO $paymentDTO)
   {
      
      //Process the request
      try {
         $paymentDTO  =  App::make(Pipeline::class)
         ->send($paymentDTO)
         ->through(
            [
               \App\Http\Services\Gateway\InitiatePaymentSteps\Step_GetPaymentAmounts::class,
               \App\Http\Services\Gateway\InitiatePaymentSteps\Step_CreatePaymentRecord::class,
               \App\Http\Services\Gateway\InitiatePaymentSteps\Step_SendPaymentsProviderRequest::class, 
               \App\Http\Services\Gateway\InitiatePaymentSteps\Step_DispatchConfirmationJob::class,
               \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
               \App\Http\Services\Gateway\Utility\Step_LogStatus::class 
            ]
         )
         ->thenReturn();
      } catch (\Throwable $e) {
         $paymentDTO->error='At get initiate payment pipeline. '.$e->getMessage();
         Log::info($paymentDTO->error);
      }

      return $paymentDTO;
      
   }

}
