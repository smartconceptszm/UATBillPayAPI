<?php

namespace App\Http\BillPay\Services\Payments;

use App\Http\BillPay\Repositories\Payments\PaymentRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class PaymentService extends BaseService
{

   public function __construct(PaymentRepo $repository)
   {
      parent::__construct($repository);
   }
   
}
