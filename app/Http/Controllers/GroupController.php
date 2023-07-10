<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\GroupService;
use App\Http\Controllers\CRUDController;

class GroupController  extends CRUDController
{
    protected $validationRules = [
            'client_id' => 'required|string',
            'name' => 'required|string|unique:groups'
        ];
    public function __construct(GroupService $theService)
    {
        parent::__construct($theService);
    }
    
}
