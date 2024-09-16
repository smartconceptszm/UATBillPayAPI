<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentsProviderCredential extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "payments_provider_credentials";

   protected $fillable=[
      'payments_provider_id','key','keyValue','description'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
