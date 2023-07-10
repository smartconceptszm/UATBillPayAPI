<?php

namespace App\Http\BillPay\Services\MoMo;

use App\Http\BillPay\Services\MoMo\Utility\StepService_GetPaymentStatus;
use Illuminate\Support\Facades\Log;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;

class ReConfirmMoMoPayment
{

   private $getPaymentStatus;
   public function __construct(StepService_GetPaymentStatus $getPaymentStatus)
   {
      $this->getPaymentStatus=$getPaymentStatus;
   }

   public function handle(BaseDTO $momoDTO): BaseDTO
   {

      try {
         for ($i = 0; $i < (int)\env('PAYMENT_REVIEW_THRESHOLD'); $i++) {
            $momoDTO = $this->getPaymentStatus->handle($momoDTO);
            if($momoDTO->paymentStatus == "PAID | NOT RECEIPTED"){
               $momoDTO =  app(Pipeline::class)
                  ->send($momoDTO)
                  ->through(
                     [
                        \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_CheckReceiptStatus::class,
                        \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_PostPaymentToClient::class,
                        \App\Http\BillPay\Services\MoMo\ConfirmPaymentSteps\Step_SendReceiptViaSMS::class,
                     ]
                  )
                  ->thenReturn();
               break;
            }else{
               if(!((\strpos($momoDTO->error,'on get transaction status'))
                  || (\strpos($momoDTO->error,"Status Code"))
                  || (\strpos($momoDTO->error,'API Get Token error'))))
               {
                  break;
               }
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
