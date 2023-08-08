<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications;

use App\Http\BillPay\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeService;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendSMSNotificationsJob;
use Illuminate\Support\Facades\Queue;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;
use Exception;

class ServiceApplications_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   private $serviceAppCreateClient;
   private $serviceTypeDetails;
   private $validateCRMInput;
   private $serviceTypes;
   public function __construct(IServiceApplicationClient $serviceAppCreateClient,
      StepService_ValidateCRMInput $validateCRMInput,
      ServiceTypeDetailService $serviceTypeDetails,
      ServiceTypeService $serviceTypes)
   {
      $this->serviceAppCreateClient = $serviceAppCreateClient;
      $this->serviceTypeDetails = $serviceTypeDetails;
      $this->validateCRMInput = $validateCRMInput;
      $this->serviceTypes = $serviceTypes;
   } 

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 4){
         $txDTO->stepProcessed=true;
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