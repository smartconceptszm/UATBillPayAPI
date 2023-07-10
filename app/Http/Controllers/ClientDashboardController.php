<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientDashboardService;
use App\Http\Controllers\CRUDIndexController;

class ClientDashboardController extends CRUDIndexController
{

    public function __construct(ClientDashboardService $theService)
    {
        parent::__construct($theService);
    }

}
