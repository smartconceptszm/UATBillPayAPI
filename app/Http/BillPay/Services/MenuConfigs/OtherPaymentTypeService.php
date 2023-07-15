<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\OtherPaymentTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class OtherPaymentTypeService extends BaseService
{

   public function __construct(OtherPaymentTypeRepo $repository)
   {
      parent::__construct($repository);
   }

}
