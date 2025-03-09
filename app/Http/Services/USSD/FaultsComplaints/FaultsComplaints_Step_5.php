<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\External\BillingClients\EnquiryHandler;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\Services\Payments\PaymentService;
use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;

class FaultsComplaints_Step_5
{

   public function __construct(
      private ComplaintSubTypeService $cSubTypeService,
      private EnquiryHandler $getCustomerAccount,
      private ComplaintTypeService $cTypeService,
      private IComplaintClient $complaintClient,
      private PaymentService $paymentService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try{

         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->customerAccount = $txDTO->subscriberInput;

         try {

            $latestPayment = $this->paymentService->findOneBy(['customerAccount' => $txDTO->customerAccount]);
            if($latestPayment){
               $txDTO->paymentAmount =  \str_replace(",", "",$latestPayment->paymentAmount);
            }
            if(!$txDTO->paymentAmount){
               $txDTO->paymentAmount = '100';
            }
            $txDTO = $this->getCustomerAccount->handle($txDTO);
         } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = USSDStatusEnum::InvalidAccount->value;
            }else{
               $txDTO->errorType = USSDStatusEnum::SystemError->value;
            }
            $txDTO->error='At get customer account to post complaint. '.$e->getMessage();
            return $txDTO;
         }

         $theComplaint = $this->cTypeService->findOneBy([
                                    'order'=>$arrCustomerJourney[\count($arrCustomerJourney)-3],
                                    'client_id'=>$txDTO->client_id,
                                 ]);
         $theSubType = $this->cSubTypeService->findOneBy([
                        'complaint_type_id'=>$theComplaint->id,
                        'order'=>$arrCustomerJourney[\count($arrCustomerJourney)-2]
                     ]); 
         if($theSubType->requiresDetails == 'YES'){
            $complaintInfo = \end($arrCustomerJourney);
         }else{
            $complaintInfo = "";
         }

         $complaintData = [
                           'customerAccount'=>$txDTO->customerAccount,
                           'address'=> $txDTO->customer['address'],
                           'complaint_subtype_id'=>$theSubType->id,
                           'complaintCode' => $theSubType->code,
                           'mobileNumber'=>$txDTO->mobileNumber,
                           'revenuePoint'=> $txDTO->revenuePoint,
                           'consumerType'=> $txDTO->consumerType,
                           'consumerTier'=> $txDTO->consumerTier,
                           'created_at'=>$txDTO->created_at,
                           'client_id'=>$txDTO->client_id,
                           'urlPrefix'=>$txDTO->urlPrefix,
                           'details'=>$complaintInfo,
                           'session_id'=>$txDTO->id
                        ];
         $caseNumber = $this->complaintClient->create($complaintData);
         $txDTO->response = "Complaint(Fault) successfully submitted. Case number: ".$caseNumber; 
         $txDTO->status =  USSDStatusEnum::Completed->value;
         
      } catch (\Throwable $e) {
         $txDTO->error = 'At complaints step 5. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }                                             
      return $txDTO;

   }

}