<?php

namespace App\Http\BillPay\Repositories\USSD;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\ShortcutCustomer;

class ShortcutCustomerRepo extends BaseRepository
{
    
   public function __construct(ShortcutCustomer $model)
   {
      parent::__construct($model);
   }

}