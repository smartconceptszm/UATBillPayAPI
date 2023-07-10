<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientCustomerDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ClientCustomerDetailService extends BaseService
{

    public function __construct(ClientCustomerDetailRepo $repository)
    {
        parent::__construct($repository);
    }

}
