<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\GroupsOfUserService;
use App\Http\Controllers\CRUDIndexController;

class GroupsOfUserController extends CRUDIndexController
{

    public function __construct(GroupsOfUserService $theService)
    {
        parent::__construct($theService);
    }
    
}
