<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\GroupRightRepo;

class GroupRightService extends BaseService
{

    public function __construct(GroupRightRepo $repository)
    {
        parent::__construct($repository);
    }

}
