<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\CustomerRepo;

class CustomerService extends BaseService
{

    public function __construct(CustomerRepo $repository)
    {
        parent::__construct($repository);
    }

}
