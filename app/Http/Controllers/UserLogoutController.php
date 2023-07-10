<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\UserLogoutService;
use App\Http\Controllers\CRUDDeleteController;

class UserLogoutController  extends CRUDDeleteController
{

    public function __construct(UserLogoutService $theService)
    { 
       parent::__construct($theService);
    }

}
