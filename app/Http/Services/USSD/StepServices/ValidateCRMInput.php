<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Enums\MNOs;
use Exception;

class ValidateCRMInput 
{

   public function handle(string $crmFieldType, string $input):string
   {
      
      if($crmFieldType == 'MOBILE'){
         $input = \str_replace(" ", "", $input);
         if(\strlen($input)!=10){
            throw new Exception("Invalid mobile number", 1);
         }
         if(MNOs::getMNO("26".\substr($input,0,3))){
            $input = "26".$input;
         }else{
            throw new Exception("Invalid mobile number", 1);
         }
      }

      if($crmFieldType == "GENERAL"){
         //payment mode validation checks
      }

      if($crmFieldType == 'READING'){
         $input = \str_replace(" ", "", $input);
         //payment mode validation checks
      }

      if($crmFieldType == 'METER'){
         //Meter number validation checks
      }

      if($crmFieldType == "PAYMENTMODE"){
         $input = \str_replace(" ", "", $input);
         //payment mode validation checks
      }

      if($crmFieldType == "APPLICATION"){
         $input = \str_replace(" ", "", $input);
         //Application Number validation checks
      }

      if($crmFieldType == "DATE"){
         $input = \str_replace(" ", "", $input);
         if(\strlen($input)!=10){
            throw new Exception("Invalid input", 1);
         }
      }

      if($crmFieldType == "NATIONALID"){
         $input = \str_replace(" ", "", $input);
         if(\strlen($input)!=11){
            throw new Exception("Invalid input", 1);
         }
      }

      if($crmFieldType == "ONEWORD"){
         $input = \str_replace(" ", "", $input);
      }

      return $input;

   }
    
}
