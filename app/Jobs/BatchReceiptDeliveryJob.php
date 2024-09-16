<?php

namespace App\Jobs;

use App\Http\Services\Web\Payments\PaymentWithReceiptToDeliverService;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class BatchReceiptDeliveryJob extends BaseJob
{

   // public $timeout = 600;

   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(
      private Array $transactions)
   {}

   /**
    * Get the middleware the job should pass through.
    *
    * @return array<int, object>
    */
   public function middleware()
   {
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(PaymentWithReceiptToDeliverService $paymentWithReceiptToDeliverService) {

      try {
         foreach ($this->transactions as $value) {
            $paymentWithReceiptToDeliverService->update($value->id);
         }
      } catch (\Throwable $e) {
         Log::error("Handling batch receipt delivery job. DETAILS: " . $e->getMessage());
      }
      
   }

}
