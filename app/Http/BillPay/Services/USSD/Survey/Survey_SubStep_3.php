<?php

namespace App\Http\BillPay\Services\USSD\Survey;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionListItemService;
use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionService;
use App\Http\BillPay\Services\MenuConfigs\SurveyService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class Survey_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $questionListItemService;
   private $questionService;
   private $surveyService;
   public function __construct(SurveyQuestionService $questionService,
      SurveyQuestionListItemService $questionListItemService,
      SurveyService $surveyService)
   {
      $this->questionListItemService = $questionListItemService;
      $this->questionService = $questionService;
      $this->surveyService = $surveyService;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if(\count(\explode("*", $txDTO->customerJourney)) == 3){
         $txDTO->stepProcessed = true;
         try {
            if($txDTO->subscriberInput != '1'){
               throw new Exception("Invalid input", 1);
            }
            $survey = $this->surveyService->findOneBy([
                              'client_id' => $txDTO->client_id,
                              'isActive' => 'YES'
                           ]);
            $txDTO->subscriberInput = $txDTO->subscriberInput."*".$survey->id;
            $surveyQuestions = $this->questionService->findAll([
                                    'survey_id' => $survey->id,
                                 ]);
            $surveyQuestion = \array_values(\array_filter($surveyQuestions, function ($record){
                        return ($record->order == 1);
                     }));
            $surveyQuestion = $surveyQuestion[0];  
            $thePrompt = $surveyQuestion->prompt;
            if($surveyQuestion->type == 'LIST'){
               $listItems = $this->questionListItemService->findAll([
                                 'survey_question_id' => $surveyQuestion->id,
                              ]);
               $thePrompt = $thePrompt."\n";
               foreach ($listItems as $value){
                  $thePrompt.=$value->order.'. '.$value->prompt."\n";
               }
            }
            $txDTO->response = $thePrompt;
         } catch (\Throwable $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At survey step 3. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

}