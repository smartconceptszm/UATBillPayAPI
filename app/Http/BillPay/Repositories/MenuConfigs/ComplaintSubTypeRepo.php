<?php

namespace App\Http\BillPay\Repositories\MenuConfigs;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ComplaintSubType;

class ComplaintSubTypeRepo extends BaseRepository
{
    
   public function __construct(ComplaintSubType $model)
   {
      parent::__construct($model);
   }

}