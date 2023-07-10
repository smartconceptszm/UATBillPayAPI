<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\USSD\UpdateDetails\ClientCallers\IUpdateDetailsClient;
use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\ClientCustomerDetailViewService;
use App\Jobs\SendSMSNotificationsJob;
use Illuminate\Support\Facades\Queue;
use App\Http\BillPay\DTOs\BaseDTO;
use Illuminate\Support\Carbon;

class UpdateDetails_SubStep_5 extends EfectivoPipelineWithBreakContract
{

   private $detailsToChange;
   private $updateClient;
   public function __construct(ClientCustomerDetailViewService $detailsToChange,
      IUpdateDetailsClient $updateClient)
   {
      $this->detailsToChange = $detailsToChange;
      $this->updateClient = $updateClient;
   } 

   protected function stepProcess(BaseDTO $txDTO)
   {

      if (\count(\explode("*", $txDTO->customerJourney)) == 5) {
         $txDTO->stepProcessed=true;
         try {
            $arrCustomerJourney = \explode("*", $txDTO->customerJourney);
            $itemToChange = $this->detailsToChange->findOneBy(['client_id'=> $txDTO->client_id,
                                    'order'=> $arrCustomerJourney[3]]);

            if($itemToChange->type == 'MOBILE'){
               $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
               if(\strlen($txDTO->subscriberInput)!=10){
                  $txDTO->error = "Invalid input";
                  $txDTO->errorType = "InvalidInput";
                  return $txDTO;
               }
            }
            
            $customerUpdateData = [
                        'customer_detail_id'=>$itemToChange->customer_detail_id,
                        'district'=> $txDTO->customer['district'],
                        'address'=> $txDTO->customer['address'],
                        'accountNumber'=>$txDTO->accountNumber,
                        'mobileNumber'=>$txDTO->mobileNumber,
                        'client_id'=>$txDTO->client_id,
                        'details'=>$txDTO->subscriberInput
                     ];

            $caseNumber = $this->updateClient->create($customerUpdateData);
            $txDTO->response = "New ".$itemToChange->name." successfully submitted. Case number: ".
                                    $caseNumber; 
            $this->sendSMSNotification($txDTO);
            $txDTO->status='COMPLETED'; 
         } catch (\Throwable $e) {
            $txDTO->error = 'At step Post updated customer details. '.$e->getMessage();
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