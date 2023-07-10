<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ClientComplaintSubTypeService;
use App\Http\Controllers\CRUDController;

class ClientComplaintSubTypeController extends CRUDController
{
    protected $validationRules=[
        'complaint_subtype_id' => 'required|string',
        'client_id' => 'required|string',
        'order' => 'required|string',
    ];

    public function __construct(ClientComplaintSubTypeService $theService)
    {
        parent::__construct($theService);
    }

}
