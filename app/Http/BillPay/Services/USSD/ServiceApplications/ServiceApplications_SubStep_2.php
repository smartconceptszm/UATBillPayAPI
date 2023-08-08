<?php

namespace App\Http\BillPay\Services\USSD\ServiceApplications;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeDetailService;
use App\Http\BillPay\Services\MenuConfigs\ServiceTypeService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class ServiceApplications_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   private $serviceTypeDetails;
   private $accountNoMenu;
   private $serviceTypes;
   public function __construct(ServiceTypeDetailService $serviceTypeDetails,
         StepService_AccountNoMenu $accountNoMenu,
         ServiceTypeService $serviceTypes)
   {
      $this->serviceTypeDetails = $serviceTypeDetails;
      $this->accountNoMenu = $accountNoMenu;
      $this->serviceTypes = $serviceTypes;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 2){
         $txDTO->stepProcessed=true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $theServiceType = $this->serviceTypes->findOneBy([
                        'client_id'=>$txDTO->client_id,
                        'order'=>$txDTO->subscriberInput
                     ]); 
            
            if(!$theServiceType->id){
               throw new Exception("Returned empty service type",1);
            } 
            if($theServiceType->onExistingAccount == 'YES'){
               $txDTO->response = $this->accountNoMenu->handle('',$txDTO->urlPrefix);
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
            $txDTO->error='At Retrieving complaint code. '.$e->getMessage();
         }

      }
      return $txDTO;

   }

}