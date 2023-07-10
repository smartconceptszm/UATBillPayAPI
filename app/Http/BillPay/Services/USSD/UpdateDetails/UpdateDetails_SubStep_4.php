<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\USSD\Utility\StepService_GetCustomerAccount;
use App\Http\BillPay\Services\ClientCustomerDetailViewService;
use App\Http\BillPay\DTOs\BaseDTO;
use Exception;
class UpdateDetails_SubStep_4 extends EfectivoPipelineWithBreakContract
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
      if(\count(\explode("*", $txDTO->customerJourney))==4){
         $txDTO->stepProcessed=true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $itemToChange = $this->detailsToChange->findOneBy(['client_id'=> $txDTO->client_id,
                                 'order'=> $txDTO->subscriberInput]
                              );
            if($itemToChange){
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
               $txDTO->response = "Update ".$itemToChange->name." on:\n". 
                                 "Acc: ".$txDTO->subscriberInput."\n".
                                 "Name: ".$txDTO->customer['name']."\n". 
                                 "Addr: ".$txDTO->customer['address']."\n". 
                                 "Mobile: ".$txDTO->customer['mobileNumber']."\n\n".
                                 $itemToChange->prompt; 
            }else{
               throw new Exception("No record found for selection", 1);
            }
         } catch (\Throwable $e) {
            if($e->getCode() == 1){
               $txDTO->errorType = 'InvalidInput';
               $txDTO->error = $e->getMessage(); 
            }else{
               $txDTO->errorType = 'SystemError';
               $txDTO->error = $e->getMessage();  
            }
         }
      }
      return $txDTO;

   }

}