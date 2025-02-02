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
      'client_id','parent_id','order','prompt','handler','billingClient','isDefault','isActive','description',
      'isPayment','receiptingHandler','cAccountCode','onOneAccount','commonAccount','customerAccountPrompt',
      'requiresReference','referencePrompt','shortcutHandler','shortcut'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
      'requiresReference' => 'NO',
      'onOneAccount' => 'NO',
      'isPayment' => 'NO',
      'isDefault' => 'NO',
      'isActive' => 'NO',
   ];

}
