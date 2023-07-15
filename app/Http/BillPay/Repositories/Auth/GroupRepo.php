<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Group;

class GroupRepo extends BaseRepository
{
    
   public function __construct(Group $group)
   {
      parent::__construct($group);
   }

}