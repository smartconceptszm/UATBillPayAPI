<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\CustomerFieldRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class CustomerFieldService extends BaseService
{

   public function __construct(CustomerFieldRepo $repository)
   {
      parent::__construct($repository);
   }

}
