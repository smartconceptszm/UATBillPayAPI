<?php

namespace App\Http\Services\USSD\CouncilPaymentHistory;

use App\Http\Services\Enums\USSDStatusEnum;
use App\Http\DTOs\BaseDTO;
use Exception;

class CouncilPaymentHistory_Step_1 
{

   public function run(BaseDTO $txDTO)
   {

      try {    
         $txDTO->response = "Get payments made using\n". 
                              "1. my number\n".
                              "2. or another number\n".
                              "0. Back";
      } catch (\Throwable $e) {
         $txDTO->error = 'Council payment history step 1. '.$e->getMessage();
         $txDTO->errorType = USSDStatusEnum::SystemError->value;
      }
      return $txDTO;
      
   }
   
}