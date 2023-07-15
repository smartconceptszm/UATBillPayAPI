<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMessage extends Model
{
   use HasFactory;

   protected $fillable=[
      'client_id','user_id','sourceFile', 'description','type' 
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
            'type' => 'BULK',
         ];
}
