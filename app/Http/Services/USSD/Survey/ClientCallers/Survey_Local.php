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

      try {
         DB::beginTransaction();
         try {
            $surveyTicket = $this->surveyEntryService->create([
                                    'accountNumber' => $surveyData['accountNumber'],
                                    'mobileNumber' => $surveyData['mobileNumber'],
                                    'survey_id' => $surveyData['survey_id'],
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
         } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
         }

         $this->sendSMSNotification($surveyData);

      } catch (Exception $e) {
         throw new Exception('Error at  create survey entry. '.$e->getMessage());
      }
      return $surveyTicket->caseNumber;                                            

   }

   private function sendSMSNotification(array $smsData): void
   {
      $arrSMSes = [
               [
                  'mobileNumber' => $smsData['mobileNumber'],
                  'client_id' => $smsData['client_id'],
                  'message' => $smsData['response'],
                  'type' => 'NOTIFICATION',
               ]
         ];
      Queue::later(Carbon::now()->addSeconds(3), 
                     new SendSMSesJob($arrSMSes,$smsData['urlPrefix']));
   }

}