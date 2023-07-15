<?php

namespace App\Http\BillPay\Services\SMS;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\SMS\MessageRepo;

class MessageService extends BaseService
{

    public function __construct(MessageRepo $repository)
    {
        parent::__construct($repository);
    }

}
