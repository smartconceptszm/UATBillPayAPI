<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DispatchReConfirmationJob extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if( $paymentDTO->paymentStatus == "PAYMENT FAILED"){
            $paymentDTO->status = "COMPLETED";
            Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY'))
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