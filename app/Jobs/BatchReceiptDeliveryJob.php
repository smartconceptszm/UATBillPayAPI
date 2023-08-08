<?php

namespace App\Jobs;

use App\Http\BillPay\Services\Payments\PaymentWithReceiptToDeliverService;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class BatchReceiptDeliveryJob extends BaseJob
{

   private $transactions;

   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(Array $transactions)
   {
      $this->transactions = $transactions;
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   public function handle(PaymentWithReceiptToDeliverService $deliverReceipt) {

      try {
         foreach ($this->transactions as $transaction) {
               $deliverReceipt->update([],$transaction['id']);
         }
      } catch (\Throwable $e) {
         Log::error("Handling batch receipt delivery job. DETAILS: " . $e->getMessage());
      }
      
   }

}
