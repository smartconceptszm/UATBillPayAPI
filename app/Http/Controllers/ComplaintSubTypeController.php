<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ComplaintSubTypeService;
use App\Http\Controllers\CRUDController;

class ComplaintSubTypeController extends CRUDController
{

    protected $validationRules = [
                        'complaint_type_id' => 'required|string',
                        'code' => 'required|string',
                        'name' => 'required|string'
                    ];

    public function __construct(ComplaintSubTypeService $theService)
    {
        parent::__construct($theService);
    }

}
