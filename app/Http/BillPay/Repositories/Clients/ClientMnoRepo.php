<?php

namespace App\Http\BillPay\Repositories\Clients;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ClientMno;

class ClientMnoRepo extends BaseRepository
{
   public function __construct(ClientMno $model)
   {
      parent::__construct($model);
   }
   
}
