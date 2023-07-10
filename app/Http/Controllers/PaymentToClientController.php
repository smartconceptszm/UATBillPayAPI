<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\PaymentToClientService;
use App\Http\Controllers\CRUDIndexController;

class PaymentToClientController extends CRUDIndexController
{

    public function __construct(PaymentToClientService $theService)
    {
        parent::__construct($theService);
    }

}
