<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\UserForgotPasswordService;
use App\Http\Controllers\CRUDCreateController;

class UserForgotPasswordController extends CRUDCreateController
{
    protected $validationRules=[
        'username' => 'required|string'
    ];

    public function __construct(UserForgotPasswordService $theService)
    {
        parent::__construct($theService);
    }

}
