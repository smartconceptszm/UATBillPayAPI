<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\ComplaintTypeService;
use App\Http\Controllers\Contracts\CRUDController;

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
