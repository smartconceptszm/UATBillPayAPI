<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRevenueCode extends Model
{
   use HasFactory;

   protected $table = "client_revenue_codes";

   protected $fillable=['menu_id','code','order','name','description'];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
