<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\MnoChargeRepo;

class MnoChargeService  extends BaseService
{

    public function __construct(MnoChargeRepo $repository)
    {
        parent::__construct($repository);
    }

}
