<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\GroupRepo;

class GroupService extends BaseService
{

    public function __construct(GroupRepo $repository)
    {
        parent::__construct($repository);
    }

}
