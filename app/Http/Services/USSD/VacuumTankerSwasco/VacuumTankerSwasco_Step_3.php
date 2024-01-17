<?php

namespace App\Http\Services\USSD\VacuumTankerSwasco;

use App\Http\DTOs\BaseDTO;
use Exception;

class VacuumTankerSwasco_Step_3
{

   public function run(BaseDTO $txDTO)
   {
      
      try {
         $txDTO->response = "Enter Amount :\n";
      } catch (\Throwable $e) {
         $txDTO->errorType = 'SystemError';
         $txDTO->error='At pay for vacuum tanker step 3. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}