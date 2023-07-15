<?php

namespace App\Http\Controllers\CRM;

use App\Http\BillPay\Services\CRM\SurveyEntryDetailService;
use App\Http\Controllers\Contracts\CRUDController;

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
