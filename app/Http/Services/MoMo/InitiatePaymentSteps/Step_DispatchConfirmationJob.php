<?php

namespace App\Http\Services\MoMo\InitiatePaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use App\Jobs\ConfirmMoMoPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class Step_DispatchConfirmationJob extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $momoDTO)
   {

      try {
         if( $momoDTO->paymentStatus == "SUBMITTED"){
            Queue::later(Carbon::now()->addSeconds(
               (int)\env($momoDTO->mnoName.'_PAYSTATUS_CHECK')),
                                    new ConfirmMoMoPaymentJob($momoDTO));
         }else{
               Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                           new ReConfirmMoMoPaymentJob($momoDTO));
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At dispatching confirmation job. '.$e->getMessage();
      }
      return $momoDTO;

   }
}