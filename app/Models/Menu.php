<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
   use HasFactory;

   protected $fillable=[
      'client_id','parent_id','order','prompt','handler', 'description','isParent','isPayment','isActive'
   ];
   
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
      'isPayment' => 'YES',
      'isActive' => 'NO',
   ];

}
