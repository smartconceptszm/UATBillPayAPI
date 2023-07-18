<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\BillPay\Services\MenuConfigs\ComplaintTypeService;
use App\Http\BillPay\DTOs\BaseDTO;

class FaultsComplaints_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   private $cSubTypeService;
   private $cTypeService;
   private $accountNoMenu;
   public function __construct(ComplaintSubTypeService $cSubTypeService,
      ComplaintTypeService $cTypeService,
      StepService_AccountNoMenu $accountNoMenu)
   {
      $this->cSubTypeService=$cSubTypeService;
      $this->cTypeService=$cTypeService;
      $this->accountNoMenu=$accountNoMenu;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==4){
         $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed=true;
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

         } catch (\Throwable $e) {
            $txDTO->error='At Get extra infomation for the complaint. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }

      }
      return $txDTO;

   }

}