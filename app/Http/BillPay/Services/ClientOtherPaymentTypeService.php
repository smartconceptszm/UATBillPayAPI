<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientOtherPaymentTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ClientOtherPaymentTypeService extends BaseService
{

    public function __construct(ClientOtherPaymentTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
