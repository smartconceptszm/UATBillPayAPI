<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClientMno extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "client_mnos";

   protected $fillable=[
      'client_id','mno_id','smsCharge'
   ];

   
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
