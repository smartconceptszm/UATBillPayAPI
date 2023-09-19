<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintSubType extends Model
{
   use HasFactory;

   protected $table = 'complaint_sub_types';

   protected $fillable=[
      'complaint_type_id','code','name', 'order','requiresDetails',
      'detailType','prompt'
   ];
   
   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

   protected $attributes = [
         'requiresDetails' => 'NO',
         'detailType' => null
      ];

}
