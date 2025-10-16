<?php

namespace App\Http\Services\Gateway\Utility;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Jobs\AddCustomerToShotcutListJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_AddCutomerToShortcutList extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {
         if($paymentDTO->channel == 'USSD'){
            AddCustomerToShotcutListJob::dispatch($paymentDTO)
                                       ->delay(Carbon::now()->addSeconds(5))
                                       ->onQueue('UATlow');
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At add customer to shortcut list. '.$e->getMessage();
      }
      return $paymentDTO;

   }

}
