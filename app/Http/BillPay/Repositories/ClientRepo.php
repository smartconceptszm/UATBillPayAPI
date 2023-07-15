<?php

namespace App\Http\BillPay\Repositories;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Client;

class ClientRepo extends BaseRepository
{

   public function __construct(Client $model)
   {
      parent::__construct($model);
   }

}
