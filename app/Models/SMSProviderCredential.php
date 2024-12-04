<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SMSProviderCredential extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "sms_provider_credentials";

   protected $fillable=[
      'mno_id','key','keyValue','description'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
