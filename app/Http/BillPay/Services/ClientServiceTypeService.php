<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientServiceTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ClientServiceTypeService extends BaseService
{

    public function __construct(ClientServiceTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
