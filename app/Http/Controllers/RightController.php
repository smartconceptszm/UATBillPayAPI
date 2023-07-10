<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\RightService;
use App\Http\Controllers\CRUDController;

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
