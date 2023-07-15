<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\SurveyRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SurveyService extends BaseService
{

   public function __construct(SurveyRepo $repository)
   {
      parent::__construct($repository);
   }

}
