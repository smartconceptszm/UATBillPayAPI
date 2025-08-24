<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSDashboardChannelTotals extends Model
{

   use HasFactory;

   protected $table = "sms_dashboard_channel_totals";

   protected $fillable=[
                  'client_id','channel','dateOfMessage','year','month','day','numberOfMessages', 'totalAmount'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
