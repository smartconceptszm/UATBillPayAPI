<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\Web\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\Web\MenuConfigs\ServiceTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_Step_2
{

   public function __construct(
      private ServiceTypeDetailService $serviceTypeDetails,
      private StepService_AccountNoMenu $accountNoMenu,
      private ServiceTypeService $serviceTypes)
   {}

   public function run(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 2){
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $theServiceType = $this->serviceTypes->findOneBy([
                        'client_id'=>$txDTO->client_id,
                        'order'=>$txDTO->subscriberInput
                     ]); 
            
            if(!$theServiceType){
               throw new Exception("Returned empty service type",1);
            } 
            $theServiceType = \is_null($theServiceType)?null: (object)$theServiceType->toArray();
            if($theServiceType->onExistingAccount == 'YES'){
               $txDTO->response = $this->accountNoMenu->handle($txDTO);
            }else{
               $serviceAppQuestions = $this->serviceTypeDetails->findAll([
                     'service_type_id'=>$theServiceType->id
                  ]);
               $applicationQuestion = \array_values(\array_filter($serviceAppQuestions, function ($record){
                                                return ($record->order == 1);
                                             }));
               $applicationQuestion = $applicationQuestion[0];  
               $txDTO->response = $applicationQuestion->prompt;
               $txDTO->customerJourney = $txDTO->customerJourney."*". $txDTO->subscriberInput;
               $txDTO->subscriberInput = " - ";
            }

         } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = "InvalidInput";
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At service application step 2. '.$e->getMessage();
         }

      }
      return $txDTO;

   }

}