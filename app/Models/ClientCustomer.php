<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClientCustomer extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "client_customers";

   protected $fillable = [
         'client_id','customerAccount','revenuePoint','consumerTier','consumerType','customerAddress'
         ,'composite','parent_id','customerName','balance'
      ];
      
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
