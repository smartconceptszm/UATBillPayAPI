<?php

namespace App\Http\BillPay\Services\USSD\UpdateDetails;

use App\Http\BillPay\Services\Contracts\EfectivoPipelineWithBreakContract;
use App\Http\BillPay\Services\ClientCustomerDetailViewService;
use App\Http\BillPay\DTOs\BaseDTO;

class UpdateDetails_SubStep_3 extends EfectivoPipelineWithBreakContract
{

   private $detailsToChange;
   public function __construct(ClientCustomerDetailViewService $detailsToChange)
   {
      $this->detailsToChange = $detailsToChange;
   }

   protected function stepProcess(BaseDTO $txDTO)
   {
      
      if(\count(\explode("*", $txDTO->customerJourney))==3){
         $txDTO->stepProcessed=true;
         try {
            $txDTO->subscriberInput = \str_replace(" ", "", $txDTO->subscriberInput);
            $detailsToChange = $this->detailsToChange->findAll(['client_id'=> $txDTO->client_id]);
            $stringMenu = "Select item to edit:\n";
            foreach ($detailsToChange as $detailType) {
               $stringMenu .= $detailType->order.'. '.$detailType->name."\n";
            }
            $txDTO->response = $stringMenu; 
         } catch (\Throwable $e) {
            $txDTO->responseMenu= "SystemError";
            $txDTO->error='At Retrieving details to update. '.$e->getMessage();
         }
      }
      return $txDTO;

   }

}