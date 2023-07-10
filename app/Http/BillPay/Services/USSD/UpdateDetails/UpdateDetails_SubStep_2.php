<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\ClientCustomerDetailViewService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;

class UpdateDetails_SubStep_2 extends EfectivoPipelineWithBreakContract
{

   private $getCustomerAccount;
   private $detailsToChange;
   public function __construct(ClientCustomerDetailViewService $detailsToChange,
      StepService_GetCustomerAccount $getCustomerAccount)
   {
      $this->getCustomerAccount = $getCustomerAccount;
      $this->detailsToChange = $detailsToChange;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {

      if(\count(\explode("*", $txDTO->customerJourney))==2){
         $txDTO->stepProcessed=true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $detailsToChange = $this->detailsToChange->findAll(['client_id'=> $txDTO->client_id]);
            if(\count($detailsToChange) > 1){
               $stringMenu = "Select item to edit:\n";
               foreach ($detailsToChange as $detailType) {
                  $stringMenu .= $detailType->order.'. '.$detailType->name."\n";
               }
               $txDTO->response = $stringMenu; 
            }

            if(\count($detailsToChange) == 1){
               try {
                  $txDTO->customer = $this->getCustomerAccount->handle(
                                          $txDTO->accountNumber,$txDTO->urlPrefix,$txDTO->client_id);
               } catch (\Throwable $e) {
                  if($e->getCode()==1){
                     $txDTO->errorType = 'InvalidAccount';
                  }else{
                     $txDTO->errorType = 'SystemError';
                  }
                  $txDTO->error=$e->getMessage();
                  return $txDTO;
               }
               $itemToChange = $detailsToChange[0];
               $txDTO->customerJourney = $txDTO->customerJourney*$txDTO->subscriberInput;
               $txDTO->subscriberInput = $itemToChange->order;
               $txDTO->response = "Update ".$itemToChange->name." on:\n". 
               "Acc: ".$txDTO->subscriberInput."\n".
               "Name: ".$txDTO->customer['name']."\n". 
               "Addr: ".$txDTO->customer['address']."\n". 
               "Mobile: ".$txDTO->customer['mobileNumber']."\n\n".
               $itemToChange->prompt; 
            }
            if(\count($detailsToChange) == 1){
               throw new Exception("No records found", 1);
            }
         } catch (\Throwable $e) {
            $txDTO->errorType = 'SystemError';
            $txDTO->error=$e->getMessage();  
         }
      }
      return $txDTO;

   }

}