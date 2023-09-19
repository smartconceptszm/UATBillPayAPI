<?php

namespace App\Http\Services\USSD\FaultsComplaints;

use App\Http\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\Services\MenuConfigs\ComplaintSubTypeService;
use App\Http\Services\MenuConfigs\ComplaintTypeService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;
use App\Jobs\SendSMSesJob;
use App\Http\DTOs\BaseDTO;
use Exception;

class FaultsComplaints_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   public function __construct(
      private ComplaintSubTypeService $cSubTypeService,
      private StepService_GetCustomerAccount $getCustomerAccount,
      private ComplaintTypeService $cTypeService,
      private IComplaintClient $complaintClient)
   {}

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 5){
         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed=true;
         try{
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $txDTO->accountNumber = $txDTO->subscriberInput;
            try {
               $txDTO->customer = $this->getCustomerAccount->handle($txDTO->accountNumber,
                                                      $txDTO->urlPrefix,$txDTO->client_id);
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
                              'details'=>$complaintInfo,
                              'session_id'=>$txDTO->id
                           ];
            $caseNumber = $this->complaintClient->create($complaintData);
            $txDTO->response = "Complaint(Fault) successfully submitted. Case number: ".
                                    $caseNumber; 
            $this->sendSMSNotification($txDTO);
            $txDTO->status='COMPLETED'; 
         } catch (Exception $e) {
            $txDTO->error = 'At complaints step 5. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }                                             
      }
      return $txDTO;

   }

   private function sendSMSNotification(BaseDTO $txDTO): void
   {
      
      $arrSMSes = [
            [
               'mobileNumber' => $txDTO->mobileNumber,
               'client_id' => $txDTO->client_id,
               'message' => $txDTO->response,
               'type' => 'NOTIFICATION',
            ]
         ];
      Queue::later(Carbon::now()->addSeconds(3), 
                     new SendSMSesJob($arrSMSes));

   }

}