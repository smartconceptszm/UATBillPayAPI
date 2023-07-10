<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Repositories\ClientComplaintSubTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ClientComplaintSubTypeService extends BaseService
{

    public function __construct(ClientComplaintSubTypeRepo $repository)
    {
        parent::__construct($repository);
    }

}
