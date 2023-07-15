<?php

namespace App\Http\Controllers\CRM;

use App\Http\BillPay\Services\CRM\SurveyEntryService;
use App\Http\Controllers\Contracts\CRUDController;

class SurveyEntryController extends CRUDController
{

   protected $validationRules=[
      'client_id' => 'required|string',
      'survey_id' => 'required|string',
      'mobileNumber' => 'required|string'
   ];

   public function __construct(SurveyEntryService $theService)
   {
      parent::__construct($theService);
   }

}
