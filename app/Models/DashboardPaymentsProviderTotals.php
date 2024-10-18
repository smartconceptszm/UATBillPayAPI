<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardPaymentsProviderTotals extends Model
{

   use HasFactory;

   protected $table = "dashboard_payments_provider_totals";

   protected $fillable=[
                  'client_id','year','month','payments_provider_id','numberOfTransactions', 'totalAmount'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
