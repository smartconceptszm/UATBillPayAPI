<?php

namespace App\Http\Services\Gateway;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Pipeline\Pipeline;
use App\Http\DTOs\BaseDTO;

class ReConfirmPayment
{

   public function handle(BaseDTO $paymentDTO): BaseDTO
   {

      try {
         $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
         for ($i = 0; $i < (int)$billpaySettings['PAYMENT_REVIEW_THRESHOLD']; $i++) {
            $paymentDTO->error = '';
            $paymentDTO = App::make(Pipeline::class)
                                 ->send($paymentDTO)
                                 ->through(
                                    [
                                       \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_GetPaymentStatus::class,
                                       \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                                       \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                                       \App\Http\Services\Gateway\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                                    ]
                                 )
                                 ->thenReturn();
            if($paymentDTO->paymentStatus == "PAYMENT FAILED"){
               if(!(
                     (\strpos($paymentDTO->error,'on get transaction status'))
                     || (\strpos($paymentDTO->error,'Token error'))
                     || (\strpos($paymentDTO->error,"Status Code"))
                     || (\strpos($paymentDTO->error,"on collect funds"))
                  ))
               {
                  break;
               }
            }else{
               break;
            }
         }
         $paymentDTO->status = 'REVIEWED';
         $paymentDTO =  App::make(Pipeline::class)
                     ->send($paymentDTO)
                     ->through(
                        [
                           \App\Http\Services\Gateway\Utility\Step_UpdateTransaction::class,  
                           \App\Http\Services\Gateway\Utility\Step_LogStatus::class,
                           \App\Http\Services\Gateway\Utility\Step_RefreshAnalytics::class 
                        ]
                     )
                     ->thenReturn();
      } catch (\Throwable $e){
         Log::error("At re-confirm payment job. " . $e->getMessage() . ' - Session: ' . $paymentDTO->sessionId);
      }
      return $paymentDTO;
      
   }

}
