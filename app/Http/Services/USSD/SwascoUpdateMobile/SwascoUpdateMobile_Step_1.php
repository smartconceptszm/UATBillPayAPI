<?php

namespace App\Http\Services\USSD\SwascoUpdateMobile;

use App\Http\Services\USSD\Utility\StepService_AccountNoMenu;
use App\Http\DTOs\BaseDTO;
use Exception;

class SwascoUpdateMobile_Step_1
{

   public function __construct(
      private StepService_AccountNoMenu $accountNoMenu)
   {}

   public function run(BaseDTO $txDTO)
   {
      
      try {
         $txDTO->response = $this->accountNoMenu->handle($txDTO);
      } catch (\Throwable $e) {
         $txDTO->error = 'Update details step 1. '.$e->getMessage();
         $txDTO->errorType = 'SystemError';
      }
      return $txDTO;

   }

}