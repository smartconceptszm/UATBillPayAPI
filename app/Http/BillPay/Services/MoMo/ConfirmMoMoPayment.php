<?php

namespace App\Http\BillPay\Services\MoMo;

use App\Http\BillPay\Services\MoMo\Utility\StepService_GetPaymentStatus;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;

class ConfirmMoMoPayment
{

   private $getPaymentStatus;
   public function __construct(StepService_GetPaymentStatus $getPaymentStatus)
   {
      $this->getPaymentStatus=$getPaymentStatus;
   }

   public function handle(BaseDTO $momoDTO)
   {

      try {
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
            $momoDTO->status="REVIEWED";
         }else{
            if((\strpos($momoDTO->error,'on get transaction status'))
               || (\strpos($momoDTO->error,"Status Code"))
               || (\strpos($momoDTO->error,'API Get Token error')))
            {
               $momoDTO->status="COMPLETED";
               Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY'))
                                                   ,new ReConfirmMoMoPaymentJob($momoDTO));
            }else{
               $momoDTO->status="REVIEWED";
            }
         }
         $momoDTO =  app(Pipeline::class)
            ->send($momoDTO)
            ->through(
                  [
                     \App\Http\BillPay\Services\MoMo\Utility\Step_UpdateTransaction::class,  
                     \App\Http\BillPay\Services\MoMo\Utility\Step_LogStatus::class 
                  ]
            )
            ->thenReturn();
      } catch (\Throwable $e) {
         $momoDTO->error='At get confirm payment pipeline. '.$e->getMessage();
      }

      return $momoDTO;

   }

}
