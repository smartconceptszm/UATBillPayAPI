<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\SurveyQuestionListRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SurveyQuestionListService extends BaseService
{

    public function __construct(SurveyQuestionListRepo $repository)
    {
        parent::__construct($repository);
    }

}
