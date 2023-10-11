<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\External\BillingClients\GetCustomerAccount;
use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_Step_5
{

   public function __construct(
      private ComplaintSubTypeService $cSubTypeService,
      private GetCustomerAccount $getCustomerAccount,
      private ComplaintTypeService $cTypeService,
      private IComplaintClient $complaintClient)
   {}

   public function run(BaseDTO $txDTO)
   {

      try{
         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
         $txDTO->accountNumber = $txDTO->subscriberInput;
         try {
            $txDTO->customer = $this->getCustomerAccount->handle($txDTO);
         } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAccount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error='At get customer account to post complaint. '.$e->getMessage();
            return $txDTO;
         }
         $theComplaint = $this->cTypeService->findOneBy([
                        'order'=>$arrCustomerJourney[2],
                        'client_id'=>$txDTO->client_id,
                     ]);
      
         $theSubType = $this->cSubTypeService->findOneBy([
                        'complaint_type_id'=>$theComplaint->id,
                        'order'=>$arrCustomerJourney[3]
                     ]); 
         if($theSubType->requiresDetails == 'YES'){
            $complaintInfo = $arrCustomerJourney[4];
         }else{
            $complaintInfo = "";
         }
         $complaintData = [
                           'complaint_subtype_id'=>$theSubType->id,
                           'complaintCode' => $theSubType->code,
                           'district'=> $txDTO->customer['district'],
                           'address'=> $txDTO->customer['address'],
                           'accountNumber'=>$txDTO->accountNumber,
                           'mobileNumber'=>$txDTO->mobileNumber,
                           'client_id'=>$txDTO->client_id,
                           'urlPrefix'=>$txDTO->urlPrefix,
                           'details'=>$complaintInfo,
                           'session_id'=>$txDTO->id
                        ];
         $caseNumber = $this->complaintClient->create($complaintData);
         $txDTO->response = "Complaint(Fault) successfully submitted. Case number: ".
                                 $caseNumber; 
         $txDTO->status='COMPLETED'; 
      } catch (Exception $e) {
         $txDTO->error = 'At complaints step 5. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }                                             
      return $txDTO;

   }

}