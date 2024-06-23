<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\Web\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\Web\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_Step_3
{

   public function __construct(
      private ComplaintSubTypeService $cSubTypeService,
      private ComplaintTypeService $cTypeService,
      private StepService_AccountNoMenu $accountNoMenu
      )
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $theComplaintType = $this->cTypeService->findOneBy([
                                       'order'=>\end($arrCustomerJourney),
                                       'client_id'=>$txDTO->client_id,
                                    ]);
         $theComplaintType = \is_null($theComplaintType)?null: (object)$theComplaintType->toArray();
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $theSubType = $this->cSubTypeService->findOneBy([
                     'complaint_type_id'=>$theComplaintType->id,
                     'order'=>$txDTO->subscriberInput
                  ]); 
         
         if(!$theSubType){
            throw new Exception("Returned empty complaint code",1);
         } 
         $theSubType = \is_null($theSubType)?null: (object)$theSubType->toArray();
         if($theSubType->requiresDetails == 'YES'){
            $txDTO->response = $theSubType->prompt;
         }else{
            $txDTO->customerJourney = $txDTO->customerJourney."*". $txDTO->subscriberInput;
            $txDTO->response = $this->accountNoMenu->handle($txDTO);
            $txDTO->subscriberInput = " - ";
         }

      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = "InvalidInput";
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At complaints step 3. '.$e->getMessage();
      }
      return $txDTO;

   }

}