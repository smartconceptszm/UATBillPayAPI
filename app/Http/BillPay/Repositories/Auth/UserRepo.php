<?php

namespace App\Http\BillPay\Repositories\Auth;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\User;

class UserRepo extends BaseRepository
{

   public function __construct(User $user)
   {
      parent::__construct($user);
   }
    
}