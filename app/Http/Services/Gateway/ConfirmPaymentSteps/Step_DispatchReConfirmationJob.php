<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ReConfirmPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DispatchReConfirmationJob extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if( $paymentDTO->paymentStatus == PaymentStatusEnum::Payment_Failed->value){
            $paymentDTO->status = "COMPLETED";
            $billpaySettings = \json_decode(Cache::get('billpaySettings',\json_encode([])), true);
            Queue::later(Carbon::now()->addMinutes((int)$billpaySettings['PAYMENT_REVIEW_DELAY'])
                                       ,new ReConfirmPaymentJob($paymentDTO),'','high');
         }else{
            $paymentDTO->status = "REVIEWED";
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At dispatching confirmation job. '.$e->getMessage();
      }
      return $paymentDTO;

   }
}