<?php

namespace App\Http\Services\USSD\NkanaOtherPayments;

use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;

class NkanaOtherPayments_Step_2
{

   public function __construct(
      private ClientMenuService $clientMenuService)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {
         $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
         $txDTO->customerJourney = $txDTO->customerJourney."*".$txDTO->subscriberInput;
         $txDTO->subscriberInput = $clientMenu->commonAccount;
         $txDTO->accountNumber = $clientMenu->commonAccount;
         $txDTO->customer['accountNumber'] = $clientMenu->commonAccount;
         $txDTO->customer['name'] = $clientMenu->description;
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         if($e->getCode()==1){
            $txDTO->errorType = "InvalidInput";
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At pay for other Nkana services step 2. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}