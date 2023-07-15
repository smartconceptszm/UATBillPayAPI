<?php

namespace App\Http\BillPay\Services\CRM;

use App\Http\BillPay\Repositories\CRM\SurveyEntryDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SurveyEntryDetailService extends BaseService
{

   public function __construct(SurveyEntryDetailRepo $repository)
   {
      parent::__construct($repository);
   }

}
