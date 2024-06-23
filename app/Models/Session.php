<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{

   use HasFactory, HasUuids;

   protected $fillable=[
      'client_id','sessionId','menu_id','mno_id', 'mobileNumber','walletNumber','accountNumber','meterNumber',
      'paymentAmount','district','customerJourney','response','status','error'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'status' => 'INITIATED',
      ];

}
