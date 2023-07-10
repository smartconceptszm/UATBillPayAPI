<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\DashboardService;
use App\Http\Controllers\CRUDIndexController;

class DashboardController extends CRUDIndexController
{

    public function __construct(DashboardService $theService)
    {
        parent::__construct($theService);
    }

}
