<?php

namespace App\Http\Services\Gateway;

use App\Http\Services\Enums\PaymentStatusEnum;
use App\Http\Services\Gateway\ConfirmPayment;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;

class ReConfirmPayment
{

   public function __construct(
      private ConfirmPayment $confirmPayment)
   {}

   public function handle(BaseDTO $paymentDTO): BaseDTO
   {

      try {

         $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);

         for ($i = 0; $i < (int)$billpaySettings['PAYMENT_REVIEW_THRESHOLD']; $i++) {

            $paymentDTO->error = '';

            $paymentDTO = $this->confirmPayment->handle($paymentDTO);
            $paymentDTO->status = 'REVIEWED';

            if($paymentDTO->paymentStatus == PaymentStatusEnum::Payment_Failed->value){
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
         
      } catch (\Throwable $e){
         Log::error("At re-confirm payment job. " . $e->getMessage() . ' - Session: ' . $paymentDTO->sessionId);
      }
      return $paymentDTO;
      
   }

}
