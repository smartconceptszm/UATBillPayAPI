<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardSnippet extends Model
{
   
   use HasFactory;

   protected $table = "dashboard_snippets";

   protected $fillable = [
         'name','title','type','generateHandler','viewHandler'
      ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
