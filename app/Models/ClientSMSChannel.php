<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClientSMSChannel extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "client_sms_channels";

   protected $fillable=[
      'client_id','sms_provider_id','description'
   ];
   
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
