<?php

namespace App\Http\Controllers\MenuConfigs;

use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionService;
use App\Http\Controllers\Contracts\CRUDController;

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
