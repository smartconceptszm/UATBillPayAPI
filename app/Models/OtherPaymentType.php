<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherPaymentType extends Model
{

   use HasFactory;

   protected $fillable=[
         'client_id','code','name','order', 'receiptAccount','ledgerAccountNumber',
         'hasReference','type','prompt','isActive'
      ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

   protected $attributes = [
         'receiptAccount' => 'CUSTOMER',
         'hasApplicationNo' => 'NO',
         'hasApplicationNo' => 'NO',
         'isActive' =>'NO'
      ];

}
