<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionListItemService;
use App\Http\Controllers\Contracts\CRUDController;

class SurveyQuestionListItemController extends CRUDController
{

    protected $validationRules=[
        'survey_question_id' => 'required|string',
        'prompt' => 'required|string',
        'order' => 'required|string'
    ];

    public function __construct(SurveyQuestionListItemService $theService)
    {
        parent::__construct($theService);
    }

}
