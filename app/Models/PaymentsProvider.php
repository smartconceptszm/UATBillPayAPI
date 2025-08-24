<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaymentsProvider extends Model
{

   use HasFactory, HasUuids;

   protected $table = 'payments_providers';

   protected $fillable=[
      'name','shortName', 'colour','contactName', 'contactEmail','contactNo',
      'logo','client_id'
   ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];
      
}
