<?php

namespace App\Http\BillPay\Repositories\Payments;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Payment;

class PaymentRepo extends BaseRepository
{
    
   public function __construct(Payment $model)
   {
      parent::__construct($model);
   }

}