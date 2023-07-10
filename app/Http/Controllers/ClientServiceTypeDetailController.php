<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientServiceTypeDetailService;
use App\Http\Controllers\CRUDController;

class ClientServiceTypeDetailController extends CRUDController
{

    protected $validationRules=[
        'client_service_type_id' => 'required|string',
        'prompt' => 'required|string',
        'order' => 'required|string',
        'name' => 'required|string'
    ];

    public function __construct(ClientServiceTypeDetailService $theService)
    {
        parent::__construct($theService);
    }

}
