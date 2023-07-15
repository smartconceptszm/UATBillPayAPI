<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\OtherPaymentType;

class OtherPaymentTypeRepo extends BaseRepository
{
    
   public function __construct(OtherPaymentType $model)
   {
      parent::__construct($model);
   }

}