<?php

namespace App\Http\BillPay\Services\USSD\Survey\ClientCallers;

use App\Http\BillPay\Services\USSD\Survey\ClientCallers\ISurveyClient;
use App\Http\BillPay\Services\MenuConfigs\SurveyQuestionService;
use App\Http\BillPay\Services\CRM\SurveyEntryDetailService;
use App\Http\BillPay\Services\CRM\SurveyEntryService;
use Illuminate\Support\Facades\DB;
use Exception;

class Survey_Local implements ISurveyClient
{

   private $surveyEntryDetailService;
   private $surveyQuestionService;
   private $surveyEntryService;
   public function __construct(SurveyEntryService $surveyEntryService,
      SurveyEntryDetailService $surveyEntryDetailService,
      SurveyQuestionService $surveyQuestionService)
   {
      $this->surveyEntryDetailService = $surveyEntryDetailService;
      $this->surveyQuestionService = $surveyQuestionService;
      $this->surveyEntryService = $surveyEntryService;
   }

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
               $surveyEntryDetail = $this->surveyEntryDetailService->create([
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
      } catch (\Exception $e) {
         throw new Exception('Error at  create survey entry. '.$e->getMessage());
      }
      return $surveyTicket->caseNumber;                                            

   }

}