<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\SurveyQuestionRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SurveyQuestionService extends BaseService
{

    public function __construct(SurveyQuestionRepo $repository)
    {
        parent::__construct($repository);
    }

}
