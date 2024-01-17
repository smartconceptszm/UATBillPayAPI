<?php

namespace App\Http\Services\USSD\Survey\ClientCallers;

use App\Http\Services\USSD\Survey\ClientCallers\ISurveyClient;
use App\Http\Services\MenuConfigs\SurveyQuestionService;
use App\Http\Services\CRM\SurveyEntryDetailService;
use App\Http\Services\CRM\SurveyEntryService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;

use Exception;

class Survey_Local implements ISurveyClient
{

   public function __construct(
      private SurveyEntryDetailService $surveyEntryDetailService,
      private SurveyQuestionService $surveyQuestionService,
      private SurveyEntryService $surveyEntryService)
   {}

   public function create(array $surveyData):string
   {

      $response = "";
      try {

         DB::beginTransaction();
         try {
            $surveyTicket = $this->surveyEntryService->create([
                                    'accountNumber' => $surveyData['accountNumber'],
                                    'mobileNumber' => $surveyData['mobileNumber'],
                                    'survey_id' => $surveyData['survey_id'],
                                    'district' => $surveyData['district'],
                                    'status' => 'SUBMITTED',
                                 ]);
            foreach ($surveyData['responses'] as $order => $value) {
               $surveyQuestion = $this->surveyQuestionService->findOneBy([
                                       'survey_id' => $surveyData['survey_id'],
                                       'order' => $order
                                    ]);
               $this->surveyEntryDetailService->create([
                                       'survey_entry_id' => $surveyTicket->id,
                                       'survey_question_id' => $surveyQuestion->id,
                                       'answer' => $value
                                    ]);
            }
            DB::commit();
         } catch (\Throwable $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
         }
         $response = "Thank you for participating in the survey. Reference number: ".$surveyTicket->caseNumber;
         // $this->sendSMSNotification([
         //                   'mobileNumber' => $surveyData['mobileNumber'],
         //                   'client_id' => $surveyData['client_id'],
         //                   'urlPrefix' => $surveyData['urlPrefix'],
         //                   'message' => $response,
         //                   'type' => 'NOTIFICATION',
         //                ]);

      } catch (\Throwable $e) {
         throw new Exception('Error at  create survey entry. '.$e->getMessage());
      }
      return  $response;                                            

   }

   private function sendSMSNotification(array $smsData): void
   {
      Queue::later(Carbon::now()->addSeconds(3), 
                     new SendSMSesJob([$smsData],$smsData['urlPrefix']));
   }

}