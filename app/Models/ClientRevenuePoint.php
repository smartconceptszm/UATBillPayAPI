<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClientRevenuePoint extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "client_revenue_points";

   protected $fillable = [
         'client_id','code','name','description'
      ];
      
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
