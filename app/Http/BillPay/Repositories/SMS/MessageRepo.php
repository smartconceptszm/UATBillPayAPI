<?php

namespace App\Http\BillPay\Repositories\SMS;

use App\Http\BillPay\Repositories\Contracts\BaseRepository;
use App\Models\Message;

class MessageRepo extends BaseRepository
{

   public function __construct(Message $model)
   {
      parent::__construct($model);
   }
    
}
