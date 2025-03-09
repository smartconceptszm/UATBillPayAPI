<?php

namespace App\Http\Services\Gateway\ConfirmPaymentSteps;

use App\Http\Services\Contracts\EfectivoPipelineContract;
use App\Http\Services\Enums\PaymentStatusEnum;
use App\Jobs\PostPaymentToClientJob;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;

class Step_DispatchReceiptingJob extends EfectivoPipelineContract
{

   protected function stepProcess(BaseDTO $paymentDTO)
   {

      try {

         if ($this->isPaymentEligibleForReceipt($paymentDTO->paymentStatus)) {
            $paymentDTO->status = "REVIEWED";
            PostPaymentToClientJob::dispatch($paymentDTO)
                                    ->delay(Carbon::now()->addSeconds(3))
                                    ->onQueue('high');
         }
      } catch (\Throwable $e) {
         $paymentDTO->error='At dispatching receipting job. '.$e->getMessage();
      }
      return $paymentDTO;

   }


   private function isPaymentEligibleForReceipt(string $paymentStatus): bool
   {
       return in_array($paymentStatus, [
                              PaymentStatusEnum::Paid->value,
                              PaymentStatusEnum::NoToken->value,
                           ]);
   }

}