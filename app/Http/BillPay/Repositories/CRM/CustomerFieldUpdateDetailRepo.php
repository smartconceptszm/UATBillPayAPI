<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\CustomerFieldUpdateDetail;

class CustomerFieldUpdateDetailRepo extends BaseRepository
{
    
   public function __construct(CustomerFieldUpdateDetail $model)
   {
      parent::__construct($model);
   }

}