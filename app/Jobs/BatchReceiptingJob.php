<?php

namespace App\Jobs;

use App\Http\Services\Payments\ClientReceiptService;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class BatchReceiptingJob extends BaseJob
{

   // public $timeout = 600;

   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(private array $transactions)
   {}

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(ClientReceiptService $clientReceiptService) {
      try {
         foreach ($this->transactions as $value) {
            $clientReceiptService->create($value);
         }
      } catch (\Throwable $e) {
         Log::error("Handling batch receipting job. DETAILS: " . $e->getMessage());
      }

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
   */
   public function failed(\Throwable $exception)
   {
      Log::error($exception->getMessage());
   }

}