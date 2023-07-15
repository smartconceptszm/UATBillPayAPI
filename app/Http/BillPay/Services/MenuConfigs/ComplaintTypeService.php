<?php

namespace App\Http\BillPay\Services\MenuConfigs;

use App\Http\BillPay\Repositories\MenuConfigs\ComplaintTypeRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class ComplaintTypeService extends BaseService
{

   public function __construct(ComplaintTypeRepo $repository)
   {
      parent::__construct($repository);
   }

}
