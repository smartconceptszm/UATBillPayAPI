<?php

namespace App\Http\BillPay\Services\Auth;

use App\Http\BillPay\Repositories\Auth\GroupRightRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class GroupRightService extends BaseService
{

   public function __construct(GroupRightRepo $repository)
   {
      parent::__construct($repository);
   }

}
