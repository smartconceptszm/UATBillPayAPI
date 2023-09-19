<?php

namespace App\Http\Services\MoMo\InitiatePaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ReConfirmMoMoPaymentJob;
use App\Jobs\ConfirmMoMoPaymentJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

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
            if((\strpos($momoDTO->mnoResponse['error'],'on collect funds'))
                     || (\strpos($momoDTO->mnoResponse['error'],"Status Code"))
                     || (\strpos($momoDTO->mnoResponse['error'],'API Get Token error')))
            {
               Queue::later(Carbon::now()->addMinutes((int)\env('PAYMENT_REVIEW_DELAY')),
                           new ReConfirmMoMoPaymentJob($momoDTO));
               if(\strlen($momoDTO->mnoResponse['error'])>65){
                     $momoDTO->error="Error on collect funds. ".$momoDTO->mnoName. " not reachable.";
               }
            }
         }
      } catch (\Throwable $e) {
         $momoDTO->error='At dispatching confirmstion job. '.$e->getMessage();
      }
      return $momoDTO;

   }
}