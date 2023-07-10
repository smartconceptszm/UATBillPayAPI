<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientServiceTypeDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ClientServiceTypeDetailService extends BaseService
{

    public function __construct(ClientServiceTypeDetailRepo $repository)
    {
        parent::__construct($repository);
    }

}
