<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentNotReceiptedService;
use App\Http\Controllers\CRUDUpdateController;

class PaymentNotReceiptedController extends CRUDUpdateController
{

    public function __construct(PaymentNotReceiptedService $theService)
    {
        parent::__construct($theService);
    }

}
