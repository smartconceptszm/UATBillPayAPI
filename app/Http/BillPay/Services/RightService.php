<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\RightRepo;


class RightService  extends BaseService
{

    public function __construct(RightRepo $repository)
    {
        parent::__construct($repository);
    }

}
