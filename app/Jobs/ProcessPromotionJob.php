<?php

namespace App\Jobs;

use App\Http\Services\Promotions\ProcessPromotionService;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class ProcessPromotionJob extends BaseJob
{

   public $timeout = 120;

   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(ProcessPromotionService $processPromotionService)
   {
      $processPromotionService->handle($this->paymentDTO);
   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}