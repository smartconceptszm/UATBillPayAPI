<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\ClientRepo;

class ClientService extends BaseService
{

    public function __construct(ClientRepo $repository)
    {
        parent::__construct($repository);
    }
    
}
