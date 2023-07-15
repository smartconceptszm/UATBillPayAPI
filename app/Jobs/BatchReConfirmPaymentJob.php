<?php

namespace App\Jobs;

use App\Http\BillPay\Services\Payments\PaymentNotConfirmedService;
use Illuminate\Support\Facades\Log;
use App\Jobs\BaseJob;

class BatchReConfirmPaymentJob extends BaseJob
{

   private $transactions;
   /**
    * Create a new job instance.
    *
    * @return void
    */
   public function __construct(array $transactions)
   {
      $this->transactions = $transactions;
   }

   /**
    * Execute the job.
    *
    * @return void
    */
   
   public function handle(PaymentNotConfirmedService $reviewTransaction) {

      try {
         foreach ($this->transactions as $transaction) {
               if ((\strpos($transaction['errorMessage'],"Status Code"))
                     || (\strpos($transaction['errorMessage'],"Status code"))
                     || (\strpos($transaction['errorMessage'],"on get transaction status"))
                     || (\strpos($transaction['errorMessage'],"Get Token error"))
                     || (\strpos($transaction['errorMessage'],"on collect funds"))) 
               {
                  Log::info("Payment review process initiated for transaction id: " . $transaction['id']);
                  $reviewTransaction->handle($transaction['id']);

               }
         }
      } catch (\Throwable $e) {
         Log::error("Handling Review Payment Batch Job. DETAILS: " . $e->getMessage());
      }

   }

}
