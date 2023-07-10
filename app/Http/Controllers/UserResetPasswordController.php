<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\UserResetPasswordService;
use App\Http\Controllers\CRUDCreateController;

class UserResetPasswordController extends CRUDCreateController
{

    protected $validationRules=[
                                    'password' => 'required|string',
                                    'username' => 'required|string',
                                    'resetPIN' => 'required|string',
                                ];

    public function __construct(UserResetPasswordService $theService)
    {
        parent::__construct($theService);
    }

}
