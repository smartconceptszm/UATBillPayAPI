<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientComplaintTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ClientComplaintTypeService extends BaseService
{

    public function __construct(ClientComplaintTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
