<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentNotMarkedReceiptedService;
use App\Http\Controllers\CRUDUpdateController;

class PaymentNotMarkedReceiptedController extends CRUDUpdateController
{

    public function __construct(PaymentNotMarkedReceiptedService $theService)
    {
        parent::__construct($theService);
    }

}
