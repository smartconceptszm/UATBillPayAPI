<?php

namespace App\Http\BillPay\Repositories\SMS;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\BulkMessage;

class BulkMessageRepo extends BaseRepository
{

   public function __construct(BulkMessage $model)
   {
      parent::__construct($model);
   }
    
}
