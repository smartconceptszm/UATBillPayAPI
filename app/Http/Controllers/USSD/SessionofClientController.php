<?php

namespace App\Http\Controllers\USSD;

use App\Http\BillPay\Services\USSD\SessionsOfClientService;
use App\Http\Controllers\Contracts\CRUDIndexController;

class SessionofClientController extends CRUDIndexController
{

    public function __construct(SessionsOfClientService $theService)
    {
        parent::__construct($theService);
    }

}
