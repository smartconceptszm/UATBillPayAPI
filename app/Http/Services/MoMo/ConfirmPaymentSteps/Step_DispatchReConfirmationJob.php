<?php

namespace App\Http\Services\MoMo\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_DispatchReConfirmationJob extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if( $momoDTO->paymentStatus == "PAYMENT FAILED"){
            $momoDTO->status = "COMPLETED";
            Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY'))
                                                ,new ReConfirmMoMoPaymentJob($momoDTO));
         }else{
            $momoDTO->status = "REVIEWED";
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At dispatching confirmation job. '.$e->getMessage();
      }
      return $momoDTO;

   }
}