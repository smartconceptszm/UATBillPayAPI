<?php

namespace App\Http\Services\Payments;

use App\Http\Services\Payments\PaymentService;
use App\Observers\PaymentObserver;
use Exception;

class PaymentUpdateService
{

   public function __construct(
      private PaymentObserver $paymentObserver,
      private PaymentService $paymentService
   ) {}

   public function handle(array $data, string $id) : object|null {

      try {
         $record = $this->paymentService->recordBeforeUpdate($id);
         $updatedRecord =  $this->paymentService->update($data,$id);
         $this->paymentObserver->manualUpdated($record,$updatedRecord,"WEB APP");
         return $updatedRecord;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }

   }

}


