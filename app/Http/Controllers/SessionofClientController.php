<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SessionsOfClientService;
use App\Http\Controllers\CRUDIndexController;

class SessionofClientController extends CRUDIndexController
{

    public function __construct(SessionsOfClientService $theService)
    {
        parent::__construct($theService);
    }

}
