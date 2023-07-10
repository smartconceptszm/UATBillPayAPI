<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\SessionRepo;

class SessionService  extends BaseService
{

    public function __construct(SessionRepo $repository)
    {
        parent::__construct($repository);
    }

}
