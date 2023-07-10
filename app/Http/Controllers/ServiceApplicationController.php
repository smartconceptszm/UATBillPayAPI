<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ServiceApplicationService;
use App\Http\Controllers\CRUDController;

class ServiceApplicationController extends CRUDController
{

    protected $validationRules=[
        'service_type_id' => 'required|string',
        'mobileNumber' => 'required|string',
        'client_id' => 'required|string',
    ];

    public function __construct(ServiceApplicationService $theService)
    {
        parent::__construct($theService);
    }

}
