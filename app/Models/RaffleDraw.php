<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleDraw extends Model
{

   use HasFactory;

   protected $table = "raffle_draws";

   protected $fillable=[
            'promotion_id','dateOfDraw','year','month','day','drawStart',
            'drawEnd','numberOfDraws','totalAmount'
         ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d',
      'updated_at' => 'datetime:Y-m-d'
   ];
   
}
