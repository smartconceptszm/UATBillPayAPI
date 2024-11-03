<?php

namespace App\Http\Services\Gateway;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class ConfirmPayment
{

   public function handle(BaseDTO $paymentDTO)
   {

      try {
         
         $paymentDTO =  App::make(Pipeline::class)
               ->send($paymentDTO)
               ->through(
                  [
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_DispatchReConfirmationJob::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                     \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                     \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                     \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                     \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class 
                  ]
               )
               ->thenReturn();
      } catch (\Throwable $e) {
         $paymentDTO->error='At get confirm payment pipeline. '.$e->getMessage();
         Log::info($paymentDTO->error);
      }

      return $paymentDTO;

   }

}
