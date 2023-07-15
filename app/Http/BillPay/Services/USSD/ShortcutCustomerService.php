<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Repositories\USSD\ShortcutCustomerRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ShortcutCustomerService extends BaseService
{

    public function __construct(ShortcutCustomerRepo $repository)
    {
        parent::__construct($repository);
    }

}
