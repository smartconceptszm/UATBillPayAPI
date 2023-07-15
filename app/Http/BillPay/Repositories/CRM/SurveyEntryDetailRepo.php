<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\SurveyEntryDetail;

class SurveyEntryDetailRepo extends BaseRepository
{
    
   public function __construct(SurveyEntryDetail $model)
   {
      parent::__construct($model);
   }

}