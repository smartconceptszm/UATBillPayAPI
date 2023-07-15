<?php

namespace App\Http\Controllers\CRM;

use App\Http\BillPay\Services\CRM\ServiceApplicationDetailService;
use App\Http\Controllers\Contracts\CRUDController;

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
