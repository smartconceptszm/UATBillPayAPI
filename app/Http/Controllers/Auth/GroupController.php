<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\GroupService;
use App\Http\Controllers\Contracts\CRUDController;

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
