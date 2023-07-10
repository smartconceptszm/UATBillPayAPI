<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\RightsOfGroupService;
use App\Http\Controllers\CRUDIndexController;

class RightsOfGroupController extends CRUDIndexController
{

    public function __construct(RightsOfGroupService $theService)
    {
        parent::__construct($theService);
    }
    
}
