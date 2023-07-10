<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ServiceTypeService;
use App\Http\Controllers\CRUDController;

class ServiceTypeController extends CRUDController
{
    protected $validationRules = [
                                    'name' => 'required|string'
                                ];

    public function __construct(ServiceTypeService $theService)
    {
        parent::__construct($theService);
    }
}
