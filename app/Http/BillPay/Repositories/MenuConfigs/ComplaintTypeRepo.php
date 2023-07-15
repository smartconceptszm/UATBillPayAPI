<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ComplaintType;

class ComplaintTypeRepo extends BaseRepository
{
    
   public function __construct(ComplaintType $model)
   {
      parent::__construct($model);
   }

}