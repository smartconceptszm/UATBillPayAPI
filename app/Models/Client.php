<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   use HasFactory, HasUuids;

   protected $fillable=[
      'shortCode','shortName','urlPrefix', 'name','balance','smsPayMode',
      'surcharge','mode','ussdAggregator','testMSISDN', 'status'
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

   protected $attributes = [
         'smsPayMode' => 'POST-PAID',
         'surcharge' => 'NO',
         'mode' => 'UP',
         'status' => 'ACTIVE',
      ];

}
