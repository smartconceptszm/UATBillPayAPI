<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\SurveyService;
use App\Http\Controllers\Contracts\CRUDController;

class SurveyController extends CRUDController
{

    protected $validationRules=[
        'client_id' => 'required|string',
        'order' => 'required|string',
        'name' => 'required|string'
    ];

    public function __construct(SurveyService $theService)
    {
        parent::__construct($theService);
    }

}
