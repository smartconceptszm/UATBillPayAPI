<?php

namespace App\Http\Services\USSD\GetLastToken;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class GetLastToken_Step_1
{

   public function __construct(
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   public function run(BaseDTO $txDTO)
   {

      try {    

         $txDTO->response = $this->accountNoMenu->handle($txDTO);
         
      } catch (\Throwable $e) {

         if($e->getCode() == 1) {
            $txDTO->error = $e->getMessage();
            $txDTO->errorType = 'PaymentProviderNotActivated';
         }else{
            $txDTO->error = 'Buy units sub step 1. '.$e->getMessage();
            $txDTO->errorType = 'SystemError';
         }

      }
      return $txDTO;
      
   }
   
}