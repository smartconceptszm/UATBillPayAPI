<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ServiceApplicationDetailService;
use App\Http\Controllers\CRUDController;

class ServiceApplicationDetailController extends CRUDController
{

    protected $validationRules=[
        'service_application_id' => 'required|string',
        'service_type_detail_id' => 'required|string',
        'value' => 'required|string',
    ];

    public function __construct(ServiceApplicationDetailService $theService)
    {
        parent::__construct($theService);
    }

}
