<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\ComplaintTypeService;
use App\Http\Controllers\CRUDController;

class ComplaintTypeController extends CRUDController
{
    protected $validationRules = [
                                    'code' => 'required|string',
                                    'name' => 'required|string'
                                ];

    public function __construct(ComplaintTypeService $theService)
    {
        parent::__construct($theService);
    }
}
