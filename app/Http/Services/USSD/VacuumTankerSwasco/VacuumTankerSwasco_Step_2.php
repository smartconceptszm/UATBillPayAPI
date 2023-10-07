<?php

namespace App\Http\Services\USSD\VacuumTankerSwasco;

use App\Http\DTOs\BaseDTO;
use Exception;

class VacuumTankerSwasco_Step_2
{

   public function run(BaseDTO $txDTO)
   {

      try {
         $txDTO->accountNumber = '320008';
         $txDTO->customer['accountNumber'] = '320008';
         $txDTO->customer['name'] = 'Vacuum Tanker Pit Emptying';
         $txDTO->response = "Enter Physical Address:\n";
      } catch (Exception $e) {
         if($e->getCode()==1){
            $txDTO->errorType = "InvalidInput";
         }else{
            $txDTO->errorType = 'SystemError';
         }
         $txDTO->error='At pay for vacuum tanker step 2. '.$e->getMessage();
      }
      return $txDTO;
      
   }

}