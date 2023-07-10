<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ServiceApplicationDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ServiceApplicationDetailService extends BaseService
{

    public function __construct(ServiceApplicationDetailRepo $repository)
    {
        parent::__construct($repository);
    }

}
