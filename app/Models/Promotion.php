<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{

   use HasFactory;

   protected $fillable=[
            'client_id','name','description','consumerType','type','entryAmount','entryMessage',
            'raffleEntryAmount','raffleEntryMessage','raffleDrawType','raffleDrawLimit',
            'raffleDrawTimeout','onDebt','rateValue','startDate','endDate','status'
         ];

   protected $casts = [
      // 'created_at' => 'datetime:Y-m-d H:i:s',
      // 'updated_at' => 'datetime:Y-m-d H:i:s',
      'created_at' => 'datetime:Y-m-d',
      'updated_at' => 'datetime:Y-m-d'
   ];

   protected $attributes = [
         'status' => 'ACTIVE',
      ];

}
