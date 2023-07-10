<?php

namespace App\Http\Controllers;

use App\Http\BillPay\Services\SurveyEntryDetailService;
use App\Http\Controllers\CRUDController;

class SurveyEntryDetailController extends CRUDController
{

    protected $validationRules=[
        'survey_entry_id' => 'required|string',
        'survey_question_id' => 'required|string',
        'answer' => 'required|string'
    ];

    public function __construct(SurveyEntryDetailService $theService)
    {
        parent::__construct($theService);
    }

}
