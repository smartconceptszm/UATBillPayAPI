<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DashboardDailyTotals extends Model
{

   use HasFactory, HasUuids;

   protected $table = "dashboard_daily_totals";

   protected $fillable=[
                  'client_id','day','numberOfTransactions', 'totalAmount'
               ];

   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
