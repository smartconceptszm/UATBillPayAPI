<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientGroupService;
use App\Http\Controllers\CRUDIndexController;

class ClientGroupController  extends CRUDIndexController
{

    public function __construct(ClientGroupService $theService)
    {
        parent::__construct($theService);
    }
    
}