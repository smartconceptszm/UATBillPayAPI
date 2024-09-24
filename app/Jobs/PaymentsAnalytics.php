<?php

namespace App\Jobs;

use App\Http\Services\Web\Payments\AnalyticsDaily;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class PaymentsAnalytics extends BaseJob
{

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(AnalyticsDaily $analyticsDaily)
   {

      $analyticsDaily->handle($this->paymentDTO);
      
   }

}