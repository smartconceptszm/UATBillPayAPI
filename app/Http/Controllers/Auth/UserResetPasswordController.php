<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\UserResetPasswordService;
use App\Http\Controllers\Contracts\CRUDCreateController;

class UserResetPasswordController extends CRUDCreateController
{

   protected $validationRules = [
                              'password' => 'required|string',
                              'username' => 'required|string',
                              'resetPIN' => 'required|string',
                           ];

   public function __construct(UserResetPasswordService $theService)
   {
      parent::__construct($theService);
   }

}
