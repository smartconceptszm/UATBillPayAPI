<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SurveyEntry extends Model
{

   use HasFactory, HasUuids;

   protected $fillable=[
      'survey_id','caseNumber','mobileNumber','customerAccount','district', 
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
