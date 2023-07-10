<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\MnoRepo;

class MnoService  extends BaseService
{

    public function __construct(MnoRepo $repository)
    {
        parent::__construct($repository);
    }

}
