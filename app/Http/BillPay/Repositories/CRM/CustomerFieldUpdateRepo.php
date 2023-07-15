<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\CustomerFieldUpdate;

class CustomerFieldUpdateRepo extends BaseRepository
{
    
   public function __construct(CustomerFieldUpdate $model)
   {
      parent::__construct($model);
   }

}