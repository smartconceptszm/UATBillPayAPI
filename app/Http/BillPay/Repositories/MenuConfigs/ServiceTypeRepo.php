<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ServiceType;

class ServiceTypeRepo extends BaseRepository
{
    
   public function __construct(ServiceType $model)
   {
      parent::__construct($model);
   }

}