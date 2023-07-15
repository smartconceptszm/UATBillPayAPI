<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ServiceApplication;

class ServiceApplicationRepo extends BaseRepository
{
    
   public function __construct(ServiceApplication $model)
   {
      parent::__construct($model);
   }

}