<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestionListType extends Model
{
   use HasFactory;

   protected $table = 'survey_question_list_types';

   protected $fillable=[
      'client_id','name','description'
   ];

   protected $casts = [
         'created_at' => 'datetime:Y-m-d H:i:s',
         'updated_at' => 'datetime:Y-m-d H:i:s',
      ];
      
}
