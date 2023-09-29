<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMenu extends Model
{
   use HasFactory;

   protected $table = "client_menus";

   protected $fillable=[
      'client_id','code','order','prompt', 'description','isPayment','isActive'
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
