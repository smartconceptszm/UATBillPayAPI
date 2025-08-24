<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SMSProvider extends Model
{

   use HasFactory, HasUuids;

   protected $table = 'sms_providers';

   protected $fillable=[
      'name','handler','payments_provider_id', 'colour','contactName', 
      'contactEmail','contactNo','logo'
   ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];
      
}
