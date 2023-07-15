<?php

namespace App\Http\BillPay\Repositories\CRM;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Complaint;

class ComplaintRepo extends BaseRepository
{
    
   public function __construct(Complaint $model)
   {
      parent::__construct($model);
   }

}