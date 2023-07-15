<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\SurveyQuestion;

class SurveyQuestionRepo extends BaseRepository
{
    
   public function __construct(SurveyQuestion $model)
   {
      parent::__construct($model);
   }

}