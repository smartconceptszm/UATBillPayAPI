<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionEntry extends Model
{

   use HasFactory;

   protected $table = "promotion_entries";

   protected $fillable=[
                  'promotion_id','payment_id','customerAccount','consumerType','mobileNumber','entryDate','paymentAmount',
                  'receiptNumber','rewardAmount','rewardRate','message','smsDelivered','status','entryDate','dateRedeemed'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

}
