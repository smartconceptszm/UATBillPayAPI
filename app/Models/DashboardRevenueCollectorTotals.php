<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardRevenueCollectorTotals extends Model
{

   use HasFactory;

   protected $table = "dashboard_revenue_collector_totals";

   protected $fillable=[
                  'client_id','revenueCollector','dateOfTransaction','year','month','day','numberOfTransactions', 'totalAmount'
               ];
               
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
