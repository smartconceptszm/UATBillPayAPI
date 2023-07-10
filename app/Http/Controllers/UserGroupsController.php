<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\UserGroupService;
use App\Http\Controllers\CRUDController;

class UserGroupsController extends CRUDController
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
