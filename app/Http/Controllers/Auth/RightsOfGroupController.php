<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\RightsOfGroupService;
use App\Http\Controllers\Contracts\CRUDIndexController;

class RightsOfGroupController extends CRUDIndexController
{

    public function __construct(RightsOfGroupService $theService)
    {
        parent::__construct($theService);
    }
    
}
