<?php

namespace App\Jobs;

use App\Http\Services\Gateway\InitiatePayment;
use App\Http\DTOs\BaseDTO;
use App\Jobs\BaseJob;

class InitiatePaymentJob extends BaseJob
{

   public $timeout = 600;
   public function __construct(
      private BaseDTO $paymentDTO)
   {}

   public function handle(InitiatePayment $initiatePayment)
   {
      
      //Handle the Job
      return $initiatePayment->handle($this->paymentDTO);

   }

}