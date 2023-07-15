<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Artisan;
use App\Jobs\BaseJob;

class RetryFailedJobs extends BaseJob
{

   public function handle()
   {
      Artisan::call('queue:retry all');
   }

}