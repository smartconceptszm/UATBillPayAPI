<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\OtherPaymentTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class OtherPaymentTypeService extends BaseService
{

    public function __construct(OtherPaymentTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
