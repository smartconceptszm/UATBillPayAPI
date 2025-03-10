<?php

namespace App\Jobs;

use App\Http\Services\Analytics\Generators\RegularAnalyticsService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class PaymentsAnalyticsRegularJob extends BaseJob
{

   public $timeout = 120;

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(RegularAnalyticsService $regularAnalyticsService)
   {
      $regularAnalyticsService->generate($this->paymentDTO);
   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}