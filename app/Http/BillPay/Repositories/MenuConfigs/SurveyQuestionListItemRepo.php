<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\SurveyQuestionListItem;

class SurveyQuestionListItemRepo extends BaseRepository
{
    
   public function __construct(SurveyQuestionListItem $model)
   {
      parent::__construct($model);
   }

}