<?php

namespace App\Http\Services\USSD\Survey;

use App\Http\Services\MenuConfigs\SurveyQuestionListItemService;
use App\Http\Services\MenuConfigs\SurveyQuestionService;
use App\Http\Services\MenuConfigs\SurveyService;
use App\Http\DTOs\BaseDTO;
use Exception;

class Survey_Step_3
{

   public function __construct(
      private SurveyQuestionListItemService $questionListItemService,
      private SurveyQuestionService $questionService,
      private SurveyService $surveyService)
   {}

   public function run(BaseDTO $txDTO)
   {
      
      try {
         
         if($txDTO->subscriberInput != '1'){
            throw new Exception("Invalid input", 1);
         }
         $survey = $this->surveyService->findOneBy([
                           'client_id' => $txDTO->client_id,
                           'isActive' => 'YES'
                        ]);
         if($survey){
            $txDTO->subscriberInput = $txDTO->subscriberInput."*".$survey->id;

            $surveyQuestion = $this->questionService->findOneBy([
                                    'survey_id' => $survey->id,
                                    'order' => '1',
                                 ]);
            $thePrompt = $surveyQuestion->prompt;
            if($surveyQuestion->type == 'LIST'){
               $listItems = $this->questionListItemService->findAll([
                                 'survey_question_list_type_id' => $surveyQuestion->survey_question_list_type_id,
                              ]);
               $thePrompt = $thePrompt."\n";
               foreach ($listItems as $item){
                  $thePrompt.=$item->order.'. '.$item->value."\n";
               }
            }
            $txDTO->response = $thePrompt;
         }else{
            throw new Exception("No active surveys", 2);
         }
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidInput';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At survey step 3. '.$e->getMessage();
      }
      return $txDTO;

   }

}