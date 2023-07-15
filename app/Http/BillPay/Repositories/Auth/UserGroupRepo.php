<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\UserGroup;

class UserGroupRepo extends BaseRepository
{

   public function __construct(UserGroup $model)
   {
      parent::__construct($model);
   }
    
}
