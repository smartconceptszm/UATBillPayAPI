<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionRate extends Model
{

   use HasFactory;

   protected $table = "promotion_rates";

   protected $fillable=[
                  'promotion_id','band','minAmount', 'maxAmount','rate',
                  'name'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
