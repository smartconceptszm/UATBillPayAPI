<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentNotMarkedReceiptedService;
use App\Http\Controllers\Contracts\CRUDUpdateController;

class PaymentNotMarkedReceiptedController extends CRUDUpdateController
{

    public function __construct(PaymentNotMarkedReceiptedService $theService)
    {
        parent::__construct($theService);
    }

}
