<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\GroupRightService;
use App\Http\Controllers\Contracts\CRUDController;

class GroupRightController extends CRUDController
{

    protected $validationRules = [
                                'group_id' => 'required|string',
                                'right_id' => 'required|string'
                            ];

    public function __construct(GroupRightService $theService)
    {
        parent::__construct($theService);
    }
}
