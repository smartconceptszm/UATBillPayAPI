<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private ComplaintSubTypeService $cSubTypeService,
      private ComplaintTypeService $cTypeService,
      private StepService_AccountNoMenu $accountNoMenu
      )
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==3){
         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed=true;
         try {

            $theComplaintType = $this->cTypeService->findOneBy([
                           'order'=>$arrCustomerJourney[2],
                           'client_id'=>$txDTO->client_id,
                        ]);

            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $theSubType = $this->cSubTypeService->findOneBy([
                        'complaint_type_id'=>$theComplaintType->id,
                        'order'=>$txDTO->subscriberInput
                     ]); 
            
            if(!$theSubType){
               throw new Exception("Returned empty complaint code",1);
            } 
            if($theSubType->requiresDetails == 'YES'){
               $txDTO->response = $theSubType->prompt;
            }else{
               $txDTO->customerJourney = $txDTO->customerJourney."*". $txDTO->subscriberInput;
               $txDTO->response = $this->accountNoMenu->handle("",$txDTO->urlPrefix);
               $txDTO->subscriberInput = " - ";
            }

         } catch (Exception $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = "InvalidInput";
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At complaints step 3. '.$e->getMessage();
         }

      }
      return $txDTO;

   }

}