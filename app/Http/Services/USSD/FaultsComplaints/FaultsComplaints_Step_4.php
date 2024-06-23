<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\USSD\Utility\StepService_ValidateCRMInput;
use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\Services\Web\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\Web\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_Step_4
{

   public function __construct(
      private StepService_ValidateCRMInput $validateInput,
      private ComplaintSubTypeService $cSubTypeService,
      private StepService_AccountNoMenu $accountNoMenu,
      private ComplaintTypeService $cTypeService
   ){}

   public function run(BaseDTO $txDTO)
   {

      try {        
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $theComplaintType = $this->cTypeService->findOneBy([
                                          'order'=>$arrCustomerJourney[\count($arrCustomerJourney)-2],
                                          'client_id'=>$txDTO->client_id,
                                       ]);
         $theComplaintType = \is_null($theComplaintType)?null: (object)$theComplaintType->toArray();
         $theSubType = $this->cSubTypeService->findOneBy([
                                    'complaint_type_id'=>$theComplaintType->id,
                                    'order'=>\end($arrCustomerJourney)
                                 ]); 
         $theSubType = \is_null($theComplaintType)?null: (object)$theSubType->toArray();
         $txDTO->subscriberInput = $this->validateInput->handle($theSubType->detailType,$txDTO->subscriberInput);
         $txDTO->response = $this->accountNoMenu->handle($txDTO);
      } catch (\Throwable $e) {
         if($e->getCode() == 1){
            $txDTO->errorType = 'InvalidInput';
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At Get extra infomation for the complaint. '.$e->getMessage();
      }
      return $txDTO;

   }

}