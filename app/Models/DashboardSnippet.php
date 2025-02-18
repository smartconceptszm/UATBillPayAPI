<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DashboardSnippet extends Model
{
   
   use HasFactory, HasUuids;

   protected $table = "dashboard_snippets";

   protected $fillable = [
         'client_id','xPosition','yPosition','sizeOnPage','title','type','generateHandler',
         'viewHandler','hasDrillDown','label','backgroundColour','borderColour',
         'isActive'
      ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
