<?php

namespace App\Jobs;

use App\Http\Services\Payments\PaymentWithReceiptToDeliverService;
use App\Jobs\Middleware\SMSClientBindJobMiddleware;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class BatchReceiptDeliveryJob extends BaseJob
{

   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(
      private Array $transactions, 
      public String $urlPrefix)
   {}

   /**
    * Get the middleware the job should pass through.
    *
    * @return array<int, object>
    */
    public function middleware(): array
    {
       return [new SMSClientBindJobMiddleware()];
    }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(PaymentWithReceiptToDeliverService $paymentWithReceiptToDeliverService) {

      try {
         foreach ($this->transactions as $transaction) {
               $paymentWithReceiptToDeliverService->update($transaction['id']);
         }
      } catch (\Throwable $e) {
         Log::error("Handling batch receipt delivery job. DETAILS: " . $e->getMessage());
      }
      
   }

}
