<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceApplication extends Model
{
   use HasFactory;

   protected $fillable=[
      'client_id','service_type_id','caseNumber', 'mobileNumber','accountNumber',
      'status','assignedBy','assignedTo','resolution','comments'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'status' => 'SUBMITTED'
      ];

}
