<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BulkMessage extends Model
{
   use HasFactory, HasUuids;

   protected $fillable=[
      'client_id','user_id','mobileNumbers', 'message','type' 
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
         'mobileNumbers' => 'array'
   ];

   protected $attributes = [
            'type' => 'BULK',
         ];
}
