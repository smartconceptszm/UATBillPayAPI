<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\SurveyQuestionListItemRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SurveyQuestionListItemService extends BaseService
{

   public function __construct(SurveyQuestionListItemRepo $repository)
   {
      parent::__construct($repository);
   }

}
