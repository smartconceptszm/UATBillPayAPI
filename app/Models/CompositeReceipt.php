<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompositeReceipt extends Model
{
   
   use HasFactory;

   protected $fillable = [
         'client_id','payment_id','customerAccount','receiptAmount','receiptNumber',
         'tokenNumber','status','error'
      ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
