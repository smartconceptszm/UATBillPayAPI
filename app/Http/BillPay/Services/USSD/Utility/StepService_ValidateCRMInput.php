<?php

namespace App\Http\BillPay\Services\USSD\Utility;

use Exception;

class StepService_ValidateCRMInput 
{

   public function handle(object $crmField, string $input):string
   {
      
      if($crmField->type == 'MOBILE'){
         $input = \str_replace(" ", "", $input);
         if(\strlen($input)!=10){
            throw new Exception("Invalid input", 1);
         }
      }

      if($crmField->type == "GENERAL"){
         //payment mode validation checks
      }

      if($crmField->type == 'READING'){
         $input = \str_replace(" ", "", $input);
         //payment mode validation checks
      }

      if($crmField->type == 'METER'){
         //Meter number validation checks
      }

      if($crmField->type == "PAYMENTMODE"){
         $input = \str_replace(" ", "", $input);
         //payment mode validation checks
      }

      if($crmField->type == "APPLICATION"){
         $input = \str_replace(" ", "", $input);
         //Application Number validation checks
      }
      return $input;

   }
    
}
