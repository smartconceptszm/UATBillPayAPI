<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentFailedService;
use App\Http\Controllers\Contracts\CRUDIndexController;

class PaymentFailedController extends CRUDIndexController
{

    public function __construct(PaymentFailedService $theService)
    {
        parent::__construct($theService);
    }

}
