<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SurveyQuestionService;
use App\Http\Controllers\CRUDController;

class SurveyQuestionController extends CRUDController
{

    protected $validationRules=[
        'survey_id' => 'required|string',
        'prompt' => 'required|string',
        'order' => 'required|string'
    ];

    public function __construct(SurveyQuestionService $theService)
    {
        parent::__construct($theService);
    }

}
