<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SurveyQuestionListService;
use App\Http\Controllers\CRUDController;

class SurveyQuestionListController extends CRUDController
{

    protected $validationRules=[
        'survey_question_id' => 'required|string',
        'prompt' => 'required|string',
        'order' => 'required|string'
    ];

    public function __construct(SurveyQuestionListService $theService)
    {
        parent::__construct($theService);
    }

}
