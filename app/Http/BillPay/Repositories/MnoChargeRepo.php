<?php

namespace App\Http\BillPay\Repositories;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\MnoCharge;

class MnoChargeRepo extends BaseRepository
{
   public function __construct(MnoCharge $model)
   {
      parent::__construct($model);
   }
   
}
