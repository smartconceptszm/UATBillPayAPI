<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\UserGroupRepo;

class UserGroupService extends BaseService
{

    public function __construct(UserGroupRepo $repository)
    {
        parent::__construct($repository);
    }

}
