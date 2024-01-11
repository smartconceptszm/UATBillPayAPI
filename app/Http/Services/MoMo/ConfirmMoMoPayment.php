<?php

namespace App\Http\Services\MoMo;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class ConfirmMoMoPayment
{

   public function handle(BaseDTO $momoDTO)
   {

      try {
         
         $momoDTO =  App::make(Pipeline::class)
               ->send($momoDTO)
               ->through(
                  [
                     \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                     \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                     \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                     \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                     \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_DispatchReConfirmationJob::class,
                     \App\Http\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                     \App\Http\Services\MoMo\Utility\Step_LogStatus::class 
                  ]
               )
               ->thenReturn();
      } catch (Exception $e) {
         $momoDTO->error='At get confirm payment pipeline. '.$e->getMessage();
         Log::info($momoDTO->error);
      }

      return $momoDTO;

   }

}
