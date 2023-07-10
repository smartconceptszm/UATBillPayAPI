<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\SurveyRepo;

class SurveyService extends BaseService
{

    public function __construct(SurveyRepo $repository)
    {
        parent::__construct($repository);
    }

}
