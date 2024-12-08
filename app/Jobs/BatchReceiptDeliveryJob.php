<?php

namespace App\Jobs;

use App\Http\Services\Payments\PaymentWithReceiptToDeliverService;
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
   public function __construct(private array $transactions)
   {}

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(PaymentWithReceiptToDeliverService $paymentWithReceiptToDeliverService) {
      try {
         foreach ($this->transactions as $value) {
            $paymentWithReceiptToDeliverService->update($value);
         }
      } catch (\Throwable $e) {
         Log::error("Handling batch receipt delivery job. DETAILS: " . $e->getMessage());
      }

   }

}
