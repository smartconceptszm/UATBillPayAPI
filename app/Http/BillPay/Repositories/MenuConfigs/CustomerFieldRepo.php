<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\CustomerField;

class CustomerFieldRepo extends BaseRepository
{
    
   public function __construct(CustomerField $model)
   {
      parent::__construct($model);
   }

}