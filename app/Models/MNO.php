<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MNO extends Model
{
   use HasFactory;

   protected $table = 'mnos';

   protected $fillable=[
      'name','colour','contactName', 'contactEmail','contactNo',
      'logo'
   ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];
      
}
