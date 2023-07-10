<?php

namespace App\Http\BillPay\Services\USSD\FaultsComplaints;

use App\Http\BillPay\Services\USSD\FaultsComplaints\ClientCallers\IComplaintClient;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\ClientComplaintSubTypeViewService;
use App\Http\BillPay\Services\ClientComplaintTypeViewService;
use App\Jobs\SendSMSNotificationsJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Carbon;

use App\Http\BillPay\DTOs\BaseDTO;

class FaultsComplaints_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   private $cCSubTypeViewService;
   private $cCTypeViewService;
   private $getCustomerAccount;
   private $complaintClient;
   public function __construct(ClientComplaintSubTypeViewService $cCSubTypeViewService,
      StepService_GetCustomerAccount $getCustomerAccount,
      ClientComplaintTypeViewService $cCTypeViewService,
      IComplaintClient $complaintClient)
   {
      $this->cCSubTypeViewService = $cCSubTypeViewService;
      $this->getCustomerAccount = $getCustomerAccount;
      $this->cCTypeViewService = $cCTypeViewService;
      $this->complaintClient = $complaintClient;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney)) == 5){
         $arrCustomerJourney=\explode("*", $txDTO->customerJourney);
         $txDTO->stepProcessed=true;
         try{
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               $txDTO->accountNumber=$txDTO->subscriberInput;
               try {
                  $txDTO->customer=$this->getCustomerAccount->handle($txDTO->accountNumber,
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
               $cCTypeView = $this->cCTypeViewService->findOneBy([
                              'order'=>$arrCustomerJourney[2],
                              'client_id'=>$txDTO->client_id,
                           ]);
            
               $cCSubTypeView = $this->cCSubTypeViewService->findOneBy([
                              'complaint_type_id'=>$cCTypeView->complaint_type_id,
                              'order'=>$arrCustomerJourney[3],
                              'client_id'=>$txDTO->client_id
                           ]); 
               if($cCSubTypeView->requiresDetails=='YES'){
                  $complaintInfo = $arrCustomerJourney[4];
               }else{
                  $complaintInfo = "";
               }
               $complaintData = [
                                 'complaint_subtype_id'=>$cCSubTypeView->id,
                                 'complaintCode' => $cCSubTypeView->code,
                                 'district'=> $txDTO->customer['district'],
                                 'address'=> $txDTO->customer['address'],
                                 'accountNumber'=>$txDTO->accountNumber,
                                 'mobileNumber'=>$txDTO->mobileNumber,
                                 'client_id'=>$txDTO->client_id,
                                 'details'=>$complaintInfo
                              ];
               $caseNumber = $this->complaintClient->create($complaintData);
               $txDTO->response = "Complaint(Fault) successfully submitted. Case number: ".
                                       $caseNumber; 
               $this->sendSMSNotification($txDTO);
               $txDTO->status='COMPLETED'; 
         } catch (\Throwable $e) {
               $txDTO->error='At step Post customer complaint. '.$e->getMessage();
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
                     new SendSMSNotificationsJob($arrSMSes));
   }

}