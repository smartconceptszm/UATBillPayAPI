<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\Controllers\Contracts\CRUDController;

class ServiceTypeDetailController extends CRUDController
{

    protected $validationRules=[
        'service_type_id' => 'required|string',
        'prompt' => 'required|string',
        'order' => 'required|string',
        'name' => 'required|string'
    ];

    public function __construct(ServiceTypeDetailService $theService)
    {
        parent::__construct($theService);
    }

}
