<?php

namespace App\Http\BillPay\Services;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\MessageRepo;

class MessageService extends BaseService
{

    public function __construct(MessageRepo $repository)
    {
        parent::__construct($repository);
    }

}
