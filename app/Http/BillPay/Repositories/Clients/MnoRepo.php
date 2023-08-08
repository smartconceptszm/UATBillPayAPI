<?php

namespace App\Http\BillPay\Repositories\Clients;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\MNO;

class MnoRepo extends BaseRepository
{
    
   public function __construct(MNO $model)
   {
      parent::__construct($model);
   }

}
