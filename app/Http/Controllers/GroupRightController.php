<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\GroupRightService;
use App\Http\Controllers\CRUDController;

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
