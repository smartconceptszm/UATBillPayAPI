<?php

namespace App\Http\BillPay\Repositories\USSD;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Session;

class SessionRepo extends BaseRepository
{

   public function __construct(Session $model)
   {
      parent::__construct($model);
   }
    
}

