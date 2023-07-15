<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\SurveyEntry;

class SurveyEntryRepo extends BaseRepository
{
    
   public function __construct(SurveyEntry $model)
   {
      parent::__construct($model);
   }

}