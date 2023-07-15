<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortcutCustomer extends Model
{
   use HasFactory;

   protected $table = "shortcut_customers";

   protected $fillable=[
      'client_id','mobileNumber','accountNumber'
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
