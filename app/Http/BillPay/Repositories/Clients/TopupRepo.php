<?php

namespace App\Http\BillPay\Repositories\Clients;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Topup;

class TopupRepo extends BaseRepository
{
   public function __construct(Topup $model)
   {
      parent::__construct($model);
   }
}
