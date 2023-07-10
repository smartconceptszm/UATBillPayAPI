<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientComplaintTypeService;
use App\Http\Controllers\CRUDController;

class ClientComplaintTypeController extends CRUDController
{

    protected $validationRules=[
        'complaint_type_id' => 'required|string',
        'client_id' => 'required|string',
        'order' => 'required|string',
    ];

    public function __construct(ClientComplaintTypeService $theService)
    {
        parent::__construct($theService);
    }

}
