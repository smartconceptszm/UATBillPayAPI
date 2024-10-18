<?php

namespace App\Http\Services\USSD\FaultsComplaints;


use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\Services\USSD\StepServices\ValidateCRMInput;
use App\Http\Services\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_Step_4
{

   public function __construct(
      private ValidateCRMInput $validateInput,
      private ComplaintSubTypeService $cSubTypeService,
      private ClientMenuService $clientMenuService,
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
         $theSubType = $this->cSubTypeService->findOneBy([
                                    'complaint_type_id'=>$theComplaintType->id,
                                    'order'=>\end($arrCustomerJourney)
                                 ]); 
         $txDTO->subscriberInput = $this->validateInput->handle($theSubType->detailType,$txDTO->subscriberInput);
         $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
         $txDTO->response = "Enter ".$clientMenu->customerAccountPrompt.":\n";
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