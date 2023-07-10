<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ServiceApplicationRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ServiceApplicationService extends BaseService
{

    public function __construct(ServiceApplicationRepo $repository)
    {
        parent::__construct($repository);
    }

}
