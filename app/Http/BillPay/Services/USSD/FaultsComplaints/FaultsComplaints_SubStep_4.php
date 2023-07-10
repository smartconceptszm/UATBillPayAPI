<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\BillPay\Services\ClientComplaintSubTypeViewService;
use App\Http\BillPay\Services\ClientComplaintTypeViewService;
use App\Http\BillPay\DTOs\BaseDTO;

class FaultsComplaints_SubStep_4 extends EfectivoPipelineWithBreakContract
{

   private $cCSubTypeViewService;
   private $cCTypeViewService;
   private $accountNoMenu;
   public function __construct(ClientComplaintSubTypeViewService $cCSubTypeViewService,
       ClientComplaintTypeViewService $cCTypeViewService,
       StepService_AccountNoMenu $accountNoMenu)
   {
       $this->cCSubTypeViewService=$cCSubTypeViewService;
       $this->cCTypeViewService=$cCTypeViewService;
       $this->accountNoMenu=$accountNoMenu;
   }

    protected function stepProcess(BaseDTO $txDTO)
    {

        if(\count(\explode("*", $txDTO->customerJourney))==4){
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $txDTO->stepProcessed=true;
            try {        
                
               $cCTypeView = $this->cCTypeViewService->findOneBy([
                              'order'=>$arrCustomerJourney[2],
                              'client_id'=>$txDTO->client_id,
                           ]);
                  
               $cCSubTypeView = $this->cCSubTypeViewService->findOneBy([
                           'complaint_type_id'=>$cCTypeView->complaint_type_id,
                           'order'=>$arrCustomerJourney[3],
                           'client_id'=>$txDTO->client_id
                        ]); 
 
                if($cCSubTypeView->detailType == 'MOBILE'){
                    $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
                    if(\strlen($txDTO->subscriberInput)!=10){
                        $txDTO->error = "Invalid input";
                        $txDTO->errorType = "InvalidInput";
                        return $txDTO;
                    }
                }

                if($cCSubTypeView->detailType == 'READING'){
                    $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
                    if((int)$txDTO->subscriberInput<1){
                        $txDTO->error = "Invalid input";
                        $txDTO->errorType = "InvalidInput";
                        return $txDTO;
                    }
                }

                if($cCSubTypeView->detailType == 'METER'){
                    //Meter number validation checks
                }

                if($cCSubTypeView->detailType == "PAYMENTMODE"){
                    $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
                    //payment mode validation checks
                }

                if($cCSubTypeView->detailType == "APPLICATION"){
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