<?php

namespace App\Http\BillPay\Services\CRM;

use App\Http\BillPay\Repositories\CRM\CustomerFieldUpdateDetailRepo;
use App\Http\BillPay\Services\Contracts\BaseService;

class CustomerFieldUpdateDetailService extends BaseService
{

   public function __construct(CustomerFieldUpdateDetailRepo $repository)
   {
      parent::__construct($repository);
   }

}
