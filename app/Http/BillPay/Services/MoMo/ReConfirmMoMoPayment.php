<?php

namespace App\Http\BillPay\Services\MoMo;

use Illuminate\Support\Facades\Log;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class ReConfirmMoMoPayment
{

   public function handle(BaseDTO $momoDTO): BaseDTO
   {

      try {
         for ($i = 0; $i < (int)\env('PAYMENT_REVIEW_THRESHOLD'); $i++) {
            $momoDTO =  app(Pipeline::class)
                        ->send($momoDTO)
                        ->through(
                           [
                              \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                              \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                              \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                              \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                           ]
                        )
                        ->thenReturn();
            if($momoDTO->paymentStatus == "PAYMENT FAILED"){
               if(!((\strpos($momoDTO->error,'on get transaction status'))
                  || (\strpos($momoDTO->error,'API Get Token error'))
                  || (\strpos($momoDTO->error,"Status Code"))))
               {
                  break;
               }
            }else{
               break;
            }
         }
         $momoDTO->status = 'REVIEWED';
         $momoDTO =  app(Pipeline::class)
            ->send($momoDTO)
            ->through(
               [
                  \App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                  \App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus::class 
               ]
            )
            ->thenReturn();
      } catch (\Throwable $e){
         Log::error("At re-confirm payment job. " . $e->getMessage() . ' - Session: ' . $momoDTO->sessionId);
      }
      return $momoDTO;
      
   }

}
