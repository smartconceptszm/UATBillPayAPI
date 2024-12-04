<?php

namespace App\Http\Services\USSD\CouncilPayment;

use App\Http\Services\USSD\StepServices\GetRevenueCollectionDetails;
use App\Http\Services\USSD\StepServices\ConfirmToPay;
use App\Http\DTOs\BaseDTO;

class CouncilPayment_Step_5
{

   public function __construct(
      private GetRevenueCollectionDetails $getRevenuePointAndCollector,
      private ConfirmToPay $confirmToPay) 
   {}

   public function run(BaseDTO $txDTO)
   {

      if($txDTO->subscriberInput == '2'){
         $txDTO->response="Enter the mobile money number to pay from(e.g 09xx xxxxxx/07xx xxxxxx)\n";
      }else{
         $txDTO = $this->getRevenuePointAndCollector->handle($txDTO);
         $txDTO = $this->confirmToPay->handle($txDTO);
      }
      if($txDTO->error !=''){
         $txDTO->error = 'Council payment step 5. '.$txDTO->error;
      }
      return $txDTO;
      
   }
    
}