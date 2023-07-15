<?php

namespace App\Http\Controllers\Auth;

use App\Http\BillPay\Services\Auth\RightService;
use App\Http\Controllers\Contracts\CRUDController;

class RightController  extends CRUDController
{
    protected $validationRules = [
                        'name' => 'required|string'
                    ];

    public function __construct(RightService $theService)
    {
        parent::__construct($theService);
    }
    
}
