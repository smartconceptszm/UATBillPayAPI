<?php

namespace App\Jobs;

use App\Http\Services\Analytics\RegularAnalyticsService;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class PaymentsAnalyticsRegular extends BaseJob
{

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(RegularAnalyticsService $RegularAnalyticsService)
   {

      $RegularAnalyticsService->generate($this->paymentDTO);
      
   }

}