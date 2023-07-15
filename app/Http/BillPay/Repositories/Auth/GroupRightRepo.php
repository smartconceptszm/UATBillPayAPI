<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\GroupRight;

class GroupRightRepo extends BaseRepository
{
    
   public function __construct(GroupRight $group_right)
   {
      parent::__construct($group_right);
   }

}