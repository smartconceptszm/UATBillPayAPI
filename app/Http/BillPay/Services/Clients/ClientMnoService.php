<?php

namespace App\Http\BillPay\Services\Clients;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\Clients\ClientMnoRepo;

class ClientMnoService  extends BaseService
{

    public function __construct(ClientMnoRepo $repository)
    {
        parent::__construct($repository);
    }

}
