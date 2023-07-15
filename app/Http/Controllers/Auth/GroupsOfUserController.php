<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\GroupsOfUserService;
use App\Http\Controllers\Contracts\CRUDIndexController;

class GroupsOfUserController extends CRUDIndexController
{

    public function __construct(GroupsOfUserService $theService)
    {
        parent::__construct($theService);
    }
    
}
