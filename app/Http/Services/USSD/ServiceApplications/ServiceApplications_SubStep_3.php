<?php

namespace App\Http\Services\USSD\ServiceApplications;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\Services\MenuConfigs\ServiceTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class ServiceApplications_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      protected GetCustomerAccount $getCustomerAccount,
      protected ServiceTypeDetailService $serviceTypeDetails,
      protected ServiceTypeService $serviceTypes)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 3){
         $txDTO->stepProcessed = true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber = $txDTO->subscriberInput;
            $txDTO->customer = $this->getCustomerAccount->handle($txDTO->accountNumber,$txDTO->urlPrefix);
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
         } catch (Exception $e) {
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