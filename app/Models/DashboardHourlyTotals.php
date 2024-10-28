<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardHourlyTotals extends Model
{

   use HasFactory;

   protected $table = "dashboard_hourly_totals";

   protected $fillable=[
                        'client_id','dateOfTransaction','hour','year',
                        'month','day','numberOfTransactions', 'totalAmount'
                     ];
               
   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
