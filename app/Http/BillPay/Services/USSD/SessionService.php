<?php

namespace App\Http\BillPay\Services\USSD;

use App\Http\BillPay\Repositories\USSD\SessionRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class SessionService  extends BaseService
{

   public function __construct(SessionRepo $repository)
   {
      parent::__construct($repository);
   }

}
