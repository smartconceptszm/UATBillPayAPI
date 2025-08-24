<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAudit extends Model
{

   use HasFactory;

   protected $table = "payment_audits";

   protected $fillable=[
                  'payment_id','oldValues','newValues', 'user_id','updateChannel'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
      'oldValues' => 'array',
      'newValues' => 'array',
   ];

   protected $attributes = [
         'updateChannel' => 'WEB APP'
      ];

}
