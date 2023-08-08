<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class ServiceApplications_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $getCustomerAccount;
   private $serviceTypeDetails;
   private $serviceTypes;
   public function __construct(StepService_GetCustomerAccount $getCustomerAccount,
      ServiceTypeDetailService $serviceTypeDetails,
      ServiceTypeService $serviceTypes)
   {
      $this->getCustomerAccount = $getCustomerAccount;
      $this->serviceTypeDetails = $serviceTypeDetails;
      $this->serviceTypes = $serviceTypes;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 3){
         $txDTO->stepProcessed = true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber = $txDTO->subscriberInput;
            $txDTO->customer = $this->getCustomerAccount->handle(
                                    $txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
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
            $txDTO->error = $e->getMessage(); 
         }
      }
      return $txDTO;

   }

}