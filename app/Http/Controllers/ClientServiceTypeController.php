<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientServiceTypeService;
use App\Http\Controllers\CRUDController;

class ClientServiceTypeController extends CRUDController
{

    protected $validationRules=[
        'service_type_id' => 'required|string',
        'client_id' => 'required|string',
        'order' => 'required|string',
    ];

    public function __construct(ClientServiceTypeService $theService)
    {
        parent::__construct($theService);
    }

}
