<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
   use HasFactory;

   protected $fillable=[
      'client_id','sessionId','mno_id', 'mobileNumber','accountNumber',
      'district','customerJourney','response','status','error'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'menu' => 'Home',
         'status' => 'INITIATED',
      ];

}
