<?php

namespace App\Http\Services\USSD\NkanaOtherPayments;

use App\Http\Services\USSD\Utility\StepService_GetAmount;
use App\Http\Services\Web\Clients\ClientMenuService;
use App\Http\DTOs\BaseDTO;
use Exception;

class NkanaOtherPayments_Step_4
{

   public function __construct(
      private ClientMenuService $clientMenuService,
      private StepService_GetAmount $getAmount)
   {}

   public function run(BaseDTO $txDTO)
   {

      try{
         try {
            [$txDTO->subscriberInput, $txDTO->paymentAmount] = $this->getAmount->handle($txDTO);
            $clientMenu = $this->clientMenuService->findById($txDTO->menu_id);
            $txDTO->response = "Pay ZMW " . $txDTO->subscriberInput . " to\n" .
                                 "Nkana Water and Sannitation Co.\n" .
                                 "For:".$clientMenu->description." \n";
            $txDTO->response .= "\nEnter\n" .
                        "1. Confirm\n" .
                        "0. Back"; 
         } catch (\Throwable $e) {
            if($e->getCode()==1){
               $txDTO->errorType = 'InvalidAmount';
            }else{
               $txDTO->errorType = 'SystemError';
            }
            $txDTO->error = $e->getMessage();
            return $txDTO;
         }   
      } catch (\Throwable $e) {
         $txDTO->errorType = 'SystemError';
         $txDTO->error = "At pay for other Nkana services step 4: ".$e->getMessage();
      }
      return $txDTO;
      
   }
}