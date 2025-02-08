<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class RetryFailedJobs extends BaseJob
{

   public function handle()
   {
      Artisan::call('queue:retry all');
   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}

}