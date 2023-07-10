<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\TopupRepo;

class TopupService   extends BaseService
{

    public function __construct(TopupRepo $repository)
    {
        parent::__construct($repository);
    }

}
