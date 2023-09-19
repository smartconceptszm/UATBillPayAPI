<?php

namespace App\Http\Services\CRM;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Carbon;
use Exception;

class SurveyResponsesOfQuestionService 
{

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         $dto = (object)$criteria;
         $records = DB::table('survey_questions as sq')
            ->join('survey_entry_details as sed','sed.survey_question_id','=','sq.id')
            ->join('survey_entries as se','se.id','=','sed.survey_entry_id')
            ->select('sq.prompt as question','se.id','se.created_at','se.accountNumber','se.mobileNumber',
                     'se.district','sed.answer')
            ->where('sq.id', '=', $dto->survey_question_id);
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
