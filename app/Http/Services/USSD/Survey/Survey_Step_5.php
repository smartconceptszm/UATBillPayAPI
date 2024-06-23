<?php

namespace App\Http\Services\USSD\Survey;

use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\Web\MenuConfigs\SurveyQuestionListItemService;
use App\Http\Services\USSD\Survey\ClientCallers\ISurveyClient;
use App\Http\Services\Web\MenuConfigs\SurveyQuestionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Http\DTOs\BaseDTO;
use Exception;

class Survey_Step_5
{

   public function __construct(
      private SurveyQuestionListItemService $questionListItemService,
      private StepService_ValidateCRMInput $validateCRMInput,
      private SurveyQuestionService $surveyQuestionService,
      private ISurveyClient $surveyCreateClient)
   {} 

   public function run(BaseDTO $txDTO)
   {

      try {

         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $surveyId = $arrCustomerJourney[4];
         $surveyResponses = \json_decode(Cache::get($txDTO->sessionId."SurveyResponses",''),true);
         $surveyResponses = $surveyResponses? $surveyResponses:[];

         $order = \count($surveyResponses) + 1;
         $surveyQuestion = $this->surveyQuestionService->findOneBy([
                                                         'survey_id' => $surveyId,
                                                         'order' => $order
                                                      ]);
         $surveyQuestion = \is_null($surveyQuestion)?null: (object)$surveyQuestion->toArray();
         $txDTO->subscriberInput = $this->validateCRMInput->handle($surveyQuestion->type,$txDTO->subscriberInput);
         if($surveyQuestion->type == 'LIST'){
            $listItem = $this->questionListItemService->findOneBy([
                              'survey_question_list_type_id' => $surveyQuestion->survey_question_list_type_id,
                              'order' => $txDTO->subscriberInput
                           ]);
            if($listItem){
               $surveyResponses[$order] = $listItem->value;
            }else{
               throw new Exception("Invalid list item selection", 1);
            }
         }else{
            $surveyResponses[$order] = $txDTO->subscriberInput;
         }

         $surveyQuestions = $this->surveyQuestionService->findAll([
                                                'survey_id' => $surveyId
                                             ]);
         if(\count($surveyQuestions) == (\count($surveyResponses))){
            $txDTO->subscriberInput = \implode(";",\array_values($surveyResponses));
            $surveyResponseData = [
                                    'accountNumber' => $txDTO->accountNumber,
                                    'mobileNumber' => $txDTO->mobileNumber,
                                    'client_id' => $txDTO->client_id,
                                    'urlPrefix' => $txDTO->urlPrefix,
                                    'survey_id' => $surveyId,
                                    'district' => $txDTO->district,
                                    'responses' => $surveyResponses
                                 ];
            $txDTO->response = $this->surveyCreateClient->create($surveyResponseData);
            $txDTO->lastResponse = true;
            $txDTO->status='COMPLETED';
         }else{
            Cache::put($txDTO->sessionId."SurveyResponses",\json_encode($surveyResponses), 
                           Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
            $surveyQuestion = \array_values(\array_filter($surveyQuestions, function ($record)use($order){
                                                               return ($record->order == $order +1);
                                                            }));
            $surveyQuestion = $surveyQuestion[0]; 
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
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $txDTO->subscriberInput = \end($arrCustomerJourney);
            \array_pop($arrCustomerJourney);
            $txDTO->customerJourney =\implode("*", $arrCustomerJourney);
         }

      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidSurveyResponse';
            Cache::forget($txDTO->sessionId."SurveyResponses");
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At survey step 5. '.$e->getMessage();
      }
      return $txDTO;

   }



}