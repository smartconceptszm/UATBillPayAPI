<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFieldUpdate extends Model
{
   use HasFactory;

   protected $fillable=[
      'client_id','customer_field_id','caseNumber', 'mobileNumber','accountNumber',
      'district','address','details', 'status','assignedBy','assignedTo',
      'resolution','comments'
   ];
   
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'status' => 'SUBMITTED'
      ];

}
