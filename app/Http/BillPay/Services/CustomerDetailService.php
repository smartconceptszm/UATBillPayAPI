<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\CustomerDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class CustomerDetailService extends BaseService
{

    public function __construct(CustomerDetailRepo $repository)
    {
        parent::__construct($repository);
    }

}
