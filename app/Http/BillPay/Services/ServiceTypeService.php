<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\ServiceTypeRepo;

class ServiceTypeService extends BaseService
{

    public function __construct(ServiceTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
