<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentNotConfirmedService;
use App\Http\Controllers\CRUDUpdateController;

class PaymentNotConfirmedController extends CRUDUpdateController
{

    public function __construct(PaymentNotConfirmedService $theService)
    {
        parent::__construct($theService);
    }

}
