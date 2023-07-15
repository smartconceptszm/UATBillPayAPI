<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Right;

class RightRepo extends BaseRepository
{
    
   public function __construct(Right $model)
   {
      parent::__construct($model);
   }

}