<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\MenuConfigs\ServiceTypeService;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_Step_2
{

   public function __construct(
      private ServiceTypeDetailService $serviceTypeDetails,
      private ClientMenuService $clientMenuService,
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
            if($theServiceType->onExistingAccount == 'YES'){
               $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
               $txDTO->response = "Enter ".$clientMenu->customerAccountPrompt.":\n";
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