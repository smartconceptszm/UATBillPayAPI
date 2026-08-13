<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\USSD\ServiceApplications\ClientCallers\IServiceApplicationClient;
use App\Http\Services\USSD\StepServices\ValidateCRMInput;
use App\Http\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\MenuConfigs\ServiceTypeService;
use App\Http\Services\Enums\USSDStatusEnum;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_Step_4
{

   public function __construct(
      private IServiceApplicationClient $serviceAppCreateClient,
      private ValidateCRMInput $validateCRMInput,
      private ServiceTypeDetailService $serviceTypeDetails,
      private ServiceTypeService $serviceTypes)
   {} 

   public function run(BaseDTO $txDTO)
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
               $serviceResponses = [
                                       'service_type_id' => $theServiceType->id,
                                       'customerAccount' => $txDTO->customerAccount,
                                       'mobileNumber' => $txDTO->mobileNumber,
                                       'client_id' => $txDTO->client_id,
                                       'responses' => $serviceAppResponses
                                    ];
               $caseNumber = $this->serviceAppCreateClient->create($serviceResponses);
               $txDTO->response = "Application for ".$theServiceType->name. " submitted. Reference number: ".
                                    $caseNumber; 
               $this->sendSMSNotification($txDTO);
               $txDTO->lastResponse = true;
               $txDTO->status =  USSDStatusEnum::Completed->value;

            }else{
               $billpaySettings = \json_decode(cache('billpaySettings',\json_encode([])), true);	
               Cache::put($txDTO->sessionId."ServicaAppResponses",\json_encode($serviceAppResponses), 
                              Carbon::now()->addMinutes(intval($billpaySettings['SESSION_CACHE'])));
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
               $txDTO->errorType = USSDStatusEnum::InvalidInput->value;
            }else{
               $txDTO->errorType = USSDStatusEnum::SystemError->value;
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
               'urlPrefix' => $txDTO->urlPrefix,
               'client_id' => $txDTO->client_id,
               'message' => $txDTO->response,
               'type' => 'NOTIFICATION',
            ]
         ];
      SendSMSesJob::dispatch($arrSMSes)
                  ->delay(Carbon::now()->addSeconds(3))
                  ->onQueue('low');
   }

}