<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\UserForgotPasswordService;
use App\Http\Controllers\Contracts\CRUDCreateController;

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
