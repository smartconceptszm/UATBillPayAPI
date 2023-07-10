<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentFailedService;
use App\Http\Controllers\CRUDIndexController;

class PaymentFailedController extends CRUDIndexController
{

    public function __construct(PaymentFailedService $theService)
    {
        parent::__construct($theService);
    }

}
