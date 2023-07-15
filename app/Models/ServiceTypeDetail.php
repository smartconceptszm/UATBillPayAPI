<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTypeDetail extends Model
{
   use HasFactory;

   protected $fillable=[
      'service_type_id','name','type','prompt','order'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'type' => 'GENERAL'
      ];

}
