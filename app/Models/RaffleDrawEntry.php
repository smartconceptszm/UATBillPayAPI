<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleDrawEntry extends Model
{

   use HasFactory;

   protected $table = "raffle_draw_entries";

   protected $fillable=[
                  'promotion_id','promotion_entry_id','customerAccount','consumerType','mobileNumber',
                  'entryDate','paymentAmount','receiptNumber','raffleDate','drawNumber',
                  'winMessage','status'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
