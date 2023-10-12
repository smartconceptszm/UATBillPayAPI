<?php

namespace App\Http\Services\CRM;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ActiveSurveyQuestionsService
{

   public function findAll(array $criteria = null):array|null
   {
      
      try {
         $user = Auth::user(); 
         $criteria['client_id'] = $user->client_id;
         $dto = (object)$criteria;
         $records = DB::table('survey_questions as sq')
            ->join('survey_entry_details as sed','sed.survey_question_id','=','sq.id')
            ->join('survey_entries as se','se.id','=','sed.survey_entry_id')
            ->join('surveys as s','s.id','=','se.survey_id')
            ->select(DB::raw('sq.survey_id,sq.id,COUNT(sed.id) as responses,sq.prompt, sq.order as number'))
            ->where('s.isActive', '=', 'YES')
            ->where('s.client_id', '=', $dto->client_id);
         $records = $records->groupBy('sq.id','sq.prompt','number');
         return $records->get()->all();
      } catch (\Throwable $e) {
         throw new Exception($e->getMessage());
      }
      
   }

}
