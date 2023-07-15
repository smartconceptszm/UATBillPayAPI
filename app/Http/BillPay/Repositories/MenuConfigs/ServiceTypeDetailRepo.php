<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ServiceTypeDetail;

class ServiceTypeDetailRepo extends BaseRepository
{
    
   public function __construct(ServiceTypeDetail $model)
   {
      parent::__construct($model);
   }

}