<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   use HasFactory;

   protected $fillable=[
      'code','shortName','urlPrefix', 'name','balance',
      'smsPayMode','surcharge','mode', 'status'
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
