<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SurveyService;
use App\Http\Controllers\CRUDController;

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
