<?php

namespace App\Http\Services\Gateway\InitiatePaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmPaymentJob;
use App\Jobs\ConfirmPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DispatchConfirmationJob extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if( $paymentDTO->paymentStatus == "SUBMITTED"){
            Queue::later(Carbon::now()->addSeconds(
               (int)\env($paymentDTO->walletHandler.'_PAYSTATUS_CHECK')),
                                    new ConfirmPaymentJob($paymentDTO),'','high');
         }else{
               Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                           new ReConfirmPaymentJob($paymentDTO),'','high');
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At dispatching confirmation job. '.$e->getMessage();
      }
      return $paymentDTO;

   }
}