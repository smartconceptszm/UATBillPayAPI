<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardDistrictTotals extends Model
{

   use HasFactory;

   protected $table = "dashboard_district_totals";

   protected $fillable=[
                  'client_id','year','month','district','numberOfTransactions', 'totalAmount'
               ];


   protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
   ];

   protected $attributes = [];

}
