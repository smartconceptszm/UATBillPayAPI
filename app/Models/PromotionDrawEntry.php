<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionDrawEntry extends Model
{

   use HasFactory;

   protected $table = "promotion_draw_entries";

   protected $fillable=[
                  'promotion_id','customerAccount','consumerType','mobileNumber','entryDate',
                  'raffleDate','raffleWinner','drawNumber','drawMessage','status','receiptNumber'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
