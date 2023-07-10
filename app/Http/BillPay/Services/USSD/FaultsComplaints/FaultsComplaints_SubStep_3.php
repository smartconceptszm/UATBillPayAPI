<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\ClientComplaintSubTypeViewService;
use App\Http\BillPay\Services\ClientComplaintTypeViewService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $cCSubTypeViewService;
   private $cCTypeViewService;
   private $accountNoMenu;
   public function __construct(ClientComplaintSubTypeViewService $cCSubTypeViewService,
         ClientComplaintTypeViewService $cCTypeViewService,
         StepService_AccountNoMenu $accountNoMenu
      )
   {
      $this->cCSubTypeViewService = $cCSubTypeViewService;
      $this->cCTypeViewService = $cCTypeViewService;
      $this->accountNoMenu = $accountNoMenu;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==3){
         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed=true;
         try {

               $cCTypeView = $this->cCTypeViewService->findOneBy([
                              'order'=>$arrCustomerJourney[2],
                              'client_id'=>$txDTO->client_id,
                           ]);

               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               $cCSubTypeView = $this->cCSubTypeViewService->findOneBy([
                           'complaint_type_id'=>$cCTypeView->complaint_type_id,
                           'order'=>$txDTO->subscriberInput,
                           'client_id'=>$txDTO->client_id
                        ]); 
               
               if(!$cCSubTypeView){
                  throw new Exception("Returned empty complaint code",1);
               } 
               if($cCSubTypeView->requiresDetails=='YES'){
                  $txDTO->response=$cCSubTypeView->prompt;
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