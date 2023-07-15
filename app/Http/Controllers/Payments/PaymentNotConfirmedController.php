<?php

namespace App\Http\Controllers\Payments;

use App\Http\BillPay\Services\Payments\PaymentNotConfirmedService;
use App\Http\Controllers\Contracts\CRUDUpdateController;

class PaymentNotConfirmedController extends CRUDUpdateController
{

    public function __construct(PaymentNotConfirmedService $theService)
    {
        parent::__construct($theService);
    }

}
