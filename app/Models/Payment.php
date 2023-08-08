<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   use HasFactory;

   protected $fillable=[
      'client_id','session_id','mno_id', 'mobileNumber','accountNumber',
      'district','mnoTransactionId','surchargeAmount','paymentAmount',
      'receiptAmount','transactionId','receiptNumber','receipt',
      'channel','paymentStatus','status','error','user_id',
      'other_payment_type_id','reference'
   ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [
         'channel' => 'USSD',
         'paymentStatus' => 'INITIATED',
         'status' => 'INITIATED',
      ];

}
