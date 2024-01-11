<?php
namespace App\Http\ScheduledTasks;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ClearFailedJobs 
{

	public function __invoke()
	{

		try {
			DB::table('failed_jobs')->delete();
		} catch (\Exception $e) {
			Log::error("In ClearFailedJobs, scheduled task: " . $e->getMessage());
		}

	}

}
