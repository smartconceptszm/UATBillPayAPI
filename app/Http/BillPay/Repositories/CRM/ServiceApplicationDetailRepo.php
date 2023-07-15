<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ServiceApplicationDetail;

class ServiceApplicationDetailRepo extends BaseRepository
{
    
   public function __construct(ServiceApplicationDetail $model)
   {
      parent::__construct($model);
   }

}