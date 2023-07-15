<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Repositories\Auth\UserGroupRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class UserGroupService extends BaseService
{

    public function __construct(UserGroupRepo $repository)
    {
        parent::__construct($repository);
    }

}
