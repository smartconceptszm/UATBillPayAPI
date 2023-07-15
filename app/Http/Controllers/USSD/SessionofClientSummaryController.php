<?php

namespace App\Http\Controllers\USSD;

use App\Http\BillPay\Services\USSD\SessionsOfClientSummaryService;
use App\Http\Controllers\Contracts\CRUDIndexController;

class SessionofClientSummaryController extends CRUDIndexController
{

    public function __construct(SessionsOfClientSummaryService $theService)
    {
        parent::__construct($theService);
    }

}
