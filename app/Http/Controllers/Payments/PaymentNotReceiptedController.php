<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentNotReceiptedService;
use App\Http\Controllers\Contracts\CRUDUpdateController;

class PaymentNotReceiptedController extends CRUDUpdateController
{

    public function __construct(PaymentNotReceiptedService $theService)
    {
        parent::__construct($theService);
    }

}
