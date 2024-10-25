<?php

namespace App\Jobs;

use App\Http\Services\Analytics\RegularAnalyticsService;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class PaymentsAnalyticsRegularJob extends BaseJob
{

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(RegularAnalyticsService $regularAnalyticsService)
   {

      $regularAnalyticsService->generate($this->paymentDTO);
      
   }

}