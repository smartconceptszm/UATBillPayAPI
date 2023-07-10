<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\PaymentRepo;

class PaymentService  extends BaseService
{

    public function __construct(PaymentRepo $repository)
    {
        parent::__construct($repository);
    }

}
