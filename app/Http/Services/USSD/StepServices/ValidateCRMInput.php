<?php

namespace App\Http\Services\USSD\StepServices;

use App\Http\Services\Enums\MNOs;
use Exception;

class ValidateCRMInput 
{
   public function handle(string $crmFieldType, string $input): string
   {
      // Remove spaces from input globally
      return match ($crmFieldType) {
         'MOBILE' => $this->validateMobileNumber($input),
         'DATE' => $this->validateDate($input),
         'NATIONALID' => $this->validateNationalId($input),
         'READING', 'PAYMENTMODE', 'APPLICATION', 'ONEWORD' => $input, // No extra validation
         'METER' => $this->validateMeterNumber($input),
         'GENERAL' => $this->validateGeneral($input),
         default => throw new Exception("Invalid CRM field type", 1),
      };
   }

   private function validateMobileNumber(string $input): string{

      $input = str_replace(" ", "", $input);
      if (!ctype_digit($input)) {
         throw new Exception("Invalid mobile number: Must contain only digits", 1);
      }

      if (strlen($input) !== 10 && strlen($input) !== 12) {
         throw new Exception("Invalid mobile number: Must be exactly 10 digits", 1);
      }

      if(strlen($input) == 10){
         $input = "26" . $input;
      }

      if(!MNOs::getMNO(substr($input, 0, 5))){
         throw new Exception("Invalid mobile number: Unknown network prefix", 1);
      }
      return $input;

   }

   private function validateDate(string $input): string
   {
      $input = str_replace(" ", "", $input);
      if (strlen($input) !== 10 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $input)) {
         throw new Exception("Invalid date: Expected format YYYY-MM-DD", 1);
      }
      return $input;
   }

   private function validateNationalId(string $input): string
   {
      $input = str_replace(" ", "", $input);
      if (!ctype_digit($input) || strlen($input) !== 11) {
         throw new Exception("Invalid National ID: Must be 11 digits", 1);
      }
      return $input;
   }

   private function validateMeterNumber(string $input): string
   {
      $input = str_replace(" ", "", $input);
      // Example: Add actual meter validation logic here if required
      if (!ctype_digit($input) || strlen($input) < 6) {
         throw new Exception("Invalid meter number", 1);
      }
      return $input;
   }

   private function validateGeneral(string $input): string
   {
      // Placeholder for future general validation logic
      return $input;
   }
}
