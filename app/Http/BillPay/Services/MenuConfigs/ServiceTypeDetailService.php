<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\ServiceTypeDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ServiceTypeDetailService extends BaseService
{

   public function __construct(ServiceTypeDetailRepo $repository)
   {
      parent::__construct($repository);
   }

}
