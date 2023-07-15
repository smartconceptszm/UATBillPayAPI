<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Services\Contracts\BaseService;
use App\Http\BillPay\Repositories\Auth\RightRepo;

class RightService  extends BaseService
{

   public function __construct(RightRepo $repository)
   {
      parent::__construct($repository);
   }

}
