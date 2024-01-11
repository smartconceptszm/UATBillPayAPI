<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

   use HasFactory, HasUuids;

   protected $fillable=[
      'mobileNumber','message','mno_id', 'client_id','bulk_id',
      'transaction_id','amount','type', 'status','user_id',
      'error'
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

   protected $attributes = [
         'type' => 'RECEIPT',
         'status' => 'INITIATED'
      ];

}
