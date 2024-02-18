<?php

namespace App\Http\Services\MoMo;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;
use Exception;

class ReConfirmMoMoPayment
{

   public function handle(BaseDTO $momoDTO): BaseDTO
   {

      try {
         for ($i = 0; $i < (int)\env('PAYMENT_REVIEW_THRESHOLD'); $i++) {
            $momoDTO->error = '';
            $momoDTO = App::make(Pipeline::class)
                     ->send($momoDTO)
                     ->through(
                        [
                           \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                           \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                           \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                           \App\Http\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                        ]
                     )
                     ->thenReturn();
            if($momoDTO->paymentStatus == "PAYMENT FAILED"){
               if(!(
                     (\strpos($momoDTO->error,'on get transaction status'))
                     || (\strpos($momoDTO->error,'Token error'))
                     || (\strpos($momoDTO->error,"Status Code"))
                     || (\strpos($momoDTO->error,"on collect funds"))
                  ))
               {
                  break;
               }
            }else{
               break;
            }
         }
         $momoDTO->status = 'REVIEWED';
         $momoDTO =  App::make(Pipeline::class)
                     ->send($momoDTO)
                     ->through(
                        [
                           \App\Http\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                           \App\Http\Services\MoMo\Utility\Step_LogStatus::class 
                        ]
                     )
                     ->thenReturn();
      } catch (\Throwable $e){
         Log::error("At re-confirm payment job. " . $e->getMessage() . ' - Session: ' . $momoDTO->sessionId);
      }
      return $momoDTO;
      
   }

}
