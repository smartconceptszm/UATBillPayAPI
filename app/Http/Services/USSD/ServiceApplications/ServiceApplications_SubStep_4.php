<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\MenuConfigs\ServiceTypeService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private IServiceApplicationClient $serviceAppCreateClient,
      private StepService_ValidateCRMInput $validateCRMInput,
      private ServiceTypeDetailService $serviceTypeDetails,
      private ServiceTypeService $serviceTypes)
   {} 

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 4){
         try {
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $theServiceType = $this->serviceTypes->findOneBy([
                  'client_id'=>$txDTO->client_id,
                  'order'=>$arrCustomerJourney[2]
               ]); 
            $serviceAppResponses = \json_decode(Cache::get($txDTO->sessionId."ServicaAppResponses",''),true);
            $serviceAppResponses = $serviceAppResponses? $serviceAppResponses:[];
            $order = \count($serviceAppResponses) + 1;
            $serviceAppQuestions = $this->serviceTypeDetails->findAll([
                  'service_type_id'=>$theServiceType->id
               ]);
            $applicationQuestion = \array_values(\array_filter($serviceAppQuestions, function ($record) use($order){
                        return ($record->order == $order);
                     }));
            $applicationQuestion = $applicationQuestion[0]; 
            $txDTO->subscriberInput = $this->validateCRMInput->handle($applicationQuestion,$txDTO->subscriberInput);
            $serviceAppResponses[$order] = $txDTO->subscriberInput;
            if(\count($serviceAppQuestions) == (\count($serviceAppResponses))){
               $txDTO->subscriberInput = \implode(";",\array_values($serviceAppResponses));
               $surveyResponse = [
                                       'service_type_id' => $theServiceType->id,
                                       'accountNumber' => $txDTO->accountNumber,
                                       'mobileNumber' => $txDTO->mobileNumber,
                                       'client_id' => $txDTO->client_id,
                                       'responses' => $serviceAppResponses
                                    ];
               $caseNumber = $this->serviceAppCreateClient->create($surveyResponse);
               $txDTO->response = "Application for ".$theServiceType->name. " submitted. Reference number: ".
                                    $caseNumber; 
               $this->sendSMSNotification($txDTO);
               $txDTO->lastResponse = true;
               $txDTO->status='COMPLETED';

            }else{
               Cache::put($txDTO->sessionId."ServicaAppResponses",\json_encode($serviceAppResponses), 
                              Carbon::now()->addMinutes(intval(\env('SESSION_CACHE'))));
               $applicationQuestion = \array_values(\array_filter($serviceAppQuestions, function ($record)use($order){
                     return ($record->order == $order +1);
                  }));
               $applicationQuestion = $applicationQuestion[0]; 
               $txDTO->response = $applicationQuestion->prompt;
               $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
               $txDTO->subscriberInput = \end($arrCustomerJourney);
                                             \array_pop($arrCustomerJourney);
               $txDTO->customerJourney =\implode("*", $arrCustomerJourney);
            }
         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidInput';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At service application step 4. '.$e->getMessage();
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
                     new SendSMSesJob($arrSMSes));
   }

}