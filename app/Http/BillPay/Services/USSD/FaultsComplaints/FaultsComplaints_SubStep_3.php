<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\BillPay\Services\MenuConfigs\ComplaintTypeService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $cSubTypeService;
   private $cTypeService;
   private $accountNoMenu;
   public function __construct(ComplaintSubTypeService $cSubTypeService,
         ComplaintTypeService $cTypeService,
         StepService_AccountNoMenu $accountNoMenu
      )
   {
      $this->cSubTypeService = $cSubTypeService;
      $this->cTypeService = $cTypeService;
      $this->accountNoMenu = $accountNoMenu;
   }

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