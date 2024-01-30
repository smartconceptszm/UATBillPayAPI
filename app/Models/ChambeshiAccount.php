<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChambeshiAccount extends Model
{
   
   use HasFactory;
   protected $primaryKey = 'AR_id';
   protected $connection = 'sqlsrvchambeshi';
   protected $table = "SK_Sage_Account_SmartConcepts";

   protected $casts = [
      'L_Cr_Date' => 'datetime:Y-m-d H:i:s'
   ];

  
}