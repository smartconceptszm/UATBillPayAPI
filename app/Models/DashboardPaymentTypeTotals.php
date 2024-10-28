<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardPaymentTypeTotals extends Model
{

   use HasFactory;

   protected $table = "dashboard_payment_type_totals";

   protected $fillable=[
                  'client_id','paymentType','dateOfTransaction','year','month','day','numberOfTransactions', 'totalAmount'
               ];

   protected $casts =[
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
