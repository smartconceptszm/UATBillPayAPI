<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MNO extends Model
{

   use HasFactory, HasUuids;

   protected $table = 'mnos';

   protected $fillable=[
      'name','payments_provider_id','colour','contactName', 'contactEmail',
      'contactNo','logo'
   ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];
      
}
