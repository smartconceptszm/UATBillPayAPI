<?php

namespace App\Http\BillPay\Services\Clients;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\Clients\ClientRepo;

class ClientService extends BaseService
{

    public function __construct(ClientRepo $repository)
    {
        parent::__construct($repository);
    }
    
}
