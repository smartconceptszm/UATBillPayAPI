<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SessionsOfClientSummaryService;
use App\Http\Controllers\CRUDIndexController;

class SessionofClientSummaryController extends CRUDIndexController
{

    public function __construct(SessionsOfClientSummaryService $theService)
    {
        parent::__construct($theService);
    }

}
