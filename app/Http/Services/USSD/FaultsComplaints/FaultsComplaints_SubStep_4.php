<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private ComplaintSubTypeService $cSubTypeService,
      private StepService_AccountNoMenu $accountNoMenu,
      private ComplaintTypeService $cTypeService
   ){}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==4){
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed = true;
         try {        
               
            $theComplaintType = $this->cTypeService->findOneBy([
                           'order'=>$arrCustomerJourney[2],
                           'client_id'=>$txDTO->client_id,
                        ]);
               
            $theSubType = $this->cSubTypeService->findOneBy([
                        'complaint_type_id'=>$theComplaintType->id,
                        'order'=>$arrCustomerJourney[3]
                     ]); 

            if($theSubType->detailType == 'MOBILE'){
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               if(\strlen($txDTO->subscriberInput)!=10){
                  $txDTO->error = "Invalid input";
                  $txDTO->errorType = "InvalidInput";
                  return $txDTO;
               }
            }

            if($theSubType->detailType == 'READING'){
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               if((int)$txDTO->subscriberInput<1){
                  $txDTO->error = "Invalid input";
                  $txDTO->errorType = "InvalidInput";
                  return $txDTO;
               }
            }

            if($theSubType->detailType == 'METER'){
               //Meter number validation checks
            }

            if($theSubType->detailType == "PAYMENTMODE"){
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               //payment mode validation checks
            }

            if($theSubType->detailType == "APPLICATION"){
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               //Application Number validation checks
            }

            $txDTO->response = $this->accountNoMenu->handle("",$txDTO->urlPrefix);

         } catch (Exception $e) {
            $txDTO->error='At Get extra infomation for the complaint. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }

      }
      return $txDTO;

   }

}