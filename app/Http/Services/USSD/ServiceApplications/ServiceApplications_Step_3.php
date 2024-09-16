<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\Web\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\Web\MenuConfigs\ServiceTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_Step_3
{

   public function __construct(
      private ServiceTypeDetailService $serviceTypeDetails,
      private EnquiryHandler $getCustomerAccount,
      private ServiceTypeService $serviceTypes)
   {}

   public function run(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 3){
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
				$txDTO->customerAccount=$txDTO->subscriberInput;
            $txDTO = $this->getCustomerAccount->handle($txDTO);
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $theServiceType = $this->serviceTypes->findOneBy([
                                    'client_id'=>$txDTO->client_id,
                                    'order'=>$arrCustomerJourney[2]
                                 ]); 

            $serviceAppQuestions = $this->serviceTypeDetails->findAll([
                                             'service_type_id'=>$theServiceType->id
                                          ]);
            $applicationQuestion = \array_values(\array_filter($serviceAppQuestions, function ($record){
                                             return ($record->order == 1);
                                          }));
            $applicationQuestion = $applicationQuestion[0];  
            $txDTO->response = $applicationQuestion->prompt;
         } catch (\Throwable $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = "At service application step 3. ". $e->getMessage(); 
         }
      }
      return $txDTO;

   }

}