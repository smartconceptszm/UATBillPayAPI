<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Survey;

class SurveyRepo extends BaseRepository
{
    
   public function __construct(Survey $model)
   {
      parent::__construct($model);
   }

}