<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Controllers\Contracts\CRUDController;

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
