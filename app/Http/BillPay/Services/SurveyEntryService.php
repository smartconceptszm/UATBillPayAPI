<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\SurveyEntryRepo;

class SurveyEntryService extends BaseService
{

    public function __construct(SurveyEntryRepo $repository)
    {
        parent::__construct($repository);
    }

}
