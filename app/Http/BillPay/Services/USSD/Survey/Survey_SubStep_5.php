<?php

namespace App\Http\BillPay\Services\USSD\Survey;

use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionListItemService;
use App\Http\BillPay\Services\USSD\Survey\ClientCallers\ISurveyClient;
use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionService;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendSMSNotificationsJob;
use Illuminate\Support\Facades\Queue;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;
use Exception;

class Survey_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   private $questionListItemService;
   private $surveyQuestionService;
   private $surveyCreateClient;
   private $getCustomerAccount;
   private $validateCRMInput;
   public function __construct(SurveyQuestionService $surveyQuestionService,
      SurveyQuestionListItemService $questionListItemService,
      StepService_GetCustomerAccount $getCustomerAccount,
      StepService_ValidateCRMInput $validateCRMInput,
      ISurveyClient $surveyCreateClient)
   {
      $this->questionListItemService = $questionListItemService;
      $this->surveyQuestionService = $surveyQuestionService;
      $this->surveyCreateClient = $surveyCreateClient;
      $this->getCustomerAccount = $getCustomerAccount;
      $this->validateCRMInput = $validateCRMInput;
   } 

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 5){
         $txDTO->stepProcessed=true;
         try {
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $surveyId = $arrCustomerJourney[4];
            $surveyResponses = \json_decode(Cache::get($txDTO->sessionId."SurveyResponses",''),true);
            $surveyResponses = $surveyResponses? $surveyResponses:[];
            $order = \count($surveyResponses) + 1;
            $surveyQuestions = $this->surveyQuestionService->findAll([
                              'survey_id' => $surveyId,
                           ]);
            $surveyQuestion = \array_values(\array_filter($surveyQuestions, function ($record) use($order){
                        return ($record->order == $order);
                     }));
            $surveyQuestion = $surveyQuestion[0]; 
            $txDTO->subscriberInput = $this->validateCRMInput->handle($surveyQuestion,$txDTO->subscriberInput);
            if($surveyQuestion->type == 'LIST'){
               $listItem = $this->questionListItemService->findOneBy([
                                 'survey_question_id' => $surveyQuestion->id,
                                 'order' => $txDTO->subscriberInput
                              ]);
               if($listItem->id){
                  $surveyResponses[$order] = $listItem->prompt;
               }else{
                  throw new Exception("Invalid list item selection", 1);
               }
            }else{
               $surveyResponses[$order] = $txDTO->subscriberInput;
            }
            if(\count($surveyQuestions) == (\count($surveyResponses))){
               $txDTO->customer = $this->getCustomerAccount->handle(
                                       $txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
               $txDTO->subscriberInput = \implode(";",\array_values($surveyResponses));
               $surveyResponse = [
                                          'accountNumber' => $txDTO->accountNumber,
                                          'mobileNumber' => $txDTO->mobileNumber,
                                          'client_id' => $txDTO->client_id,
                                          'survey_id' => $surveyId,
                                          'district' => $txDTO->customer['district'],
                                          'responses' => $surveyResponses
                                    ];
               $caseNumber = $this->surveyCreateClient->create($surveyResponse);
               $txDTO->response = "Thank you for participating in the survey. Ref. number: ".
                                    $caseNumber; 
               $this->sendSMSNotification($txDTO);
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
                                    'survey_question_id' => $surveyQuestion->id,
                                 ]);
                  $thePrompt = $thePrompt."\n";
                  foreach ($listItems as $value){
                     $thePrompt.=$value->order.'. '.$value->prompt."\n";
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
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At survey step 4. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

   private function sendSMSNotification(BaseDTO $txDTO): void
   {
      $arrSMSes = [
               [
                  'mobileNumber' => $txDTO->mobileNumber,
                  'client_id' => $txDTO->client_id,
                  'message' => $txDTO->response,
                  'type' => 'NOTIFICATION',
               ]
         ];
      Queue::later(Carbon::now()->addSeconds(3), 
                     new SendSMSNotificationsJob($arrSMSes));
   }

}