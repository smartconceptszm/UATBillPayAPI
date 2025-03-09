<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDashboardSnippet extends Model
{
   
   use HasFactory;

   protected $table = "client_dashboard_snippets";

   protected $fillable = [
         'dashboard_id','dashboard_snippet_id','rowNumber','columnNumber','sizeOnPage',
         'viewHandler','hyperlink','isActive'
      ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

}
