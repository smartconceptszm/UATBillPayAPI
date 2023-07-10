<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\SurveyEntryDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SurveyEntryDetailService extends BaseService
{

    public function __construct(SurveyEntryDetailRepo $repository)
    {
        parent::__construct($repository);
    }

}
