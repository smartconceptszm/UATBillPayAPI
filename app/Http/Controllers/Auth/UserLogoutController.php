<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Contracts\CRUDDeleteController;
use App\Http\BillPay\Services\Auth\UserLogoutService;

class UserLogoutController  extends CRUDDeleteController
{

   public function __construct(UserLogoutService $theService)
   { 
      parent::__construct($theService);
   }

}
