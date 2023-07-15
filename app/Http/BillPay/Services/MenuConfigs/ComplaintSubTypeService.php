<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\ComplaintSubTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ComplaintSubTypeService extends BaseService
{

    public function __construct(ComplaintSubTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
