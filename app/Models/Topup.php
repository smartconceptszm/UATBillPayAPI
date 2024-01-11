<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{

   use HasFactory,HasUuids;

   protected $fillable=[
      'client_id','amount', 'approval_status','initiatedBy','approvedBy'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'approval_status' => 'PENDING'
      ];
      
}
