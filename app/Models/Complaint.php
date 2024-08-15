<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
   use HasFactory, HasUuids;

   protected $fillable=[
      'complaint_subtype_id','client_id','session_id','caseNumber', 'mobileNumber','customerAccount',
      'district','address','details', 'status','assignedBy','assignedTo','resolution','comments'
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

   protected $attributes = [
         'status' => 'SUBMITTED'
      ];

}
