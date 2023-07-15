<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\UserGroupService;
use App\Http\Controllers\Contracts\CRUDController;

class UserGroupController extends CRUDController
{

    protected $validationRules=[
        'group_id' => 'required|string',
        'user_id' => 'required|string'
    ];

    public function __construct(UserGroupService $theService)
    {
        parent::__construct($theService);
    }

}
