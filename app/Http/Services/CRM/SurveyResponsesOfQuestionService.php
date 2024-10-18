<?php

namespace App\Http\Services\CRM;

use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Carbon;
use Exception;

class SurveyResponsesOfQuestionService 
{

   public function findAll(array $criteria):array|null
   {
      
      try {
         
         $dto = (object)$criteria;
         $records = DB::table('survey_entries as se')
            ->join('survey_entry_details as sed','sed.survey_entry_id','=','se.id')
            ->join('survey_questions as sq','sq.id','=','sed.survey_question_id')
            ->select('sq.survey_id','sq.prompt as question','se.id','se.created_at',
                        'se.customerAccount','se.mobileNumber','se.district','sed.answer')
            ->where('se.survey_id', '=', $dto->survey_id)
            ->where('sq.id', '=', $dto->survey_question_id);

         // $records = DB::table('survey_questions as sq')
         //    ->join('survey_entry_details as sed','sed.survey_question_id','=','sq.id')
         //    ->join('survey_entries as se','se.id','=','sed.survey_entry_id')
         //    ->select('sq.survey_id','sq.prompt as question','se.id','se.created_at','se.customerAccount','se.mobileNumber',
         //             'se.district','sed.answer')
         //    ->where('sq.survey_id', '=', $dto->survey_id)
         //    ->where('sq.id', '=', $dto->survey_question_id)
         //    ->where('sq.survey_id', '=', $dto->survey_id);
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
