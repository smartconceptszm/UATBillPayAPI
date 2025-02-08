<?php

namespace App\Jobs;

use App\Http\Services\Gateway\InitiatePayment;
use Illuminate\Support\Facades\Log;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class InitiatePaymentJob extends BaseJob
{

   // public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(InitiatePayment $initiatePayment)
   {
      
      //Handle the Job
      return $initiatePayment->handle($this->paymentDTO);

   }

   /**
     * Prevent the job from being saved in the failed_jobs table
	*/
	public function failed(\Throwable $exception)
	{
		Log::error($exception->getMessage());
	}


}