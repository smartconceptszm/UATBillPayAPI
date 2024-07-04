<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ClientMenu extends Model
{
   use HasFactory, HasUuids;

   protected $table = "client_menus";

   protected $fillable=[
      'client_id','parent_id','order','prompt','handler','billingClient','enquiryHandler', 
      'description','isPayment','accountType','receiptingHandler','isDefault','isActive',
      'onOneAccount','commonAccount','servicePoint','servicePointPrompt',
      'requiresReference','referencePrompt'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
      'accountType'  => 'POST-PAID',
      'requiresReference' => 'NO',
      'onOneAccount' => 'NO',
      'isPayment' => 'NO',
      'isDefault' => 'NO',
      'isActive' => 'NO',
   ];

}
