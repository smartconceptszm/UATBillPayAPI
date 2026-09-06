<?php

namespace App\Http\Services\PublicAPI;

use App\Models\Payment;
use Exception;

class PaymentService
{

   public function __construct(
      private Payment $model
   ) {}


   public function findById(string $id) : object|null {
      try {
         $item = $this->model->findOrFail($id, ['id', 'customerAccount', 'paymentAmount',
                                                   'receiptNumber','tokenNumber','receipt','paymentStatus',
                                                   'mobileNumber','walletNumber','ppTransactionId','error'
                                                   ]);

         $item = \is_null($item)?null:(object)$item->toArray();
         return $item;
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
   }


}


